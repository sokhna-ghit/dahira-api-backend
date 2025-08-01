<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class OrangeMoneyService
{
    private $clientId;
    private $clientSecret;
    private $apiKey;
    private $baseUrl;
    private $countryCode;
    private $environment;

    public function __construct()
    {
        $this->clientId = config('services.orange.client_id');
        $this->clientSecret = config('services.orange.client_secret');
        $this->apiKey = config('services.orange.api_key');
        $this->baseUrl = config('services.orange.base_url');
        $this->countryCode = config('services.orange.country_code');
        $this->environment = config('services.orange.environment');
        
        // URLs différentes selon l'environnement
        if ($this->environment === 'sandbox') {
            $this->baseUrl = 'https://api.orange.com/sandbox';
        }
    }

    /**
     * Obtenir un token d'accès OAuth2
     * 
     * Orange Money utilise OAuth2 pour l'authentification.
     * Le token est valide pendant 1 heure et est mis en cache.
     */
    public function getAccessToken()
    {
        try {
            // Vérifier si on a déjà un token en cache
            $cacheKey = 'orange_money_token';
            $token = Cache::get($cacheKey);
            
            if ($token) {
                Log::info('🟡 Orange Money: Token trouvé en cache');
                return $token;
            }

            Log::info('🔑 Orange Money: Demande d\'un nouveau token d\'accès');

            // Demander un nouveau token
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])->asForm()->post($this->baseUrl . '/oauth/v3/token', [
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'];
                
                // Mettre en cache pour 50 minutes (token valide 1h)
                Cache::put($cacheKey, $token, now()->addMinutes(50));
                
                Log::info('✅ Orange Money: Token d\'accès obtenu avec succès');
                return $token;
            } else {
                Log::error('❌ Orange Money: Échec obtention token', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new Exception('Impossible d\'obtenir le token Orange Money');
            }
        } catch (Exception $e) {
            Log::error('❌ Orange Money: Erreur getAccessToken', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Initier un paiement Orange Money
     * 
     * @param array $paymentData - Données du paiement
     * @return array - Réponse de l'API Orange
     */
    public function initiatePayment($paymentData)
    {
        try {
            $token = $this->getAccessToken();
            
            Log::info('💰 Orange Money: Initiation du paiement', $paymentData);

            // Préparer les données pour l'API Orange
            $requestData = [
                'merchant' => [
                    'category' => '1520', // Code pour services financiers
                    'id' => 'DAHIRA_' . config('app.name'), // Identifiant marchand
                ],
                'reference' => $paymentData['reference'],
                'subscriber' => [
                    'country' => $this->countryCode,
                    'id' => $paymentData['telephone'], // Numéro Orange Money
                    'id_type' => 'MSISDN'
                ],
                'transaction' => [
                    'amount' => $paymentData['montant'],
                    'currency' => 'XOF', // Franc CFA
                    'id' => $paymentData['transaction_id'],
                ],
                'description' => $paymentData['description'] ?? 'Paiement cotisation dahira'
            ];

            // Appel à l'API Orange Money
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/orange-money-webpay/dev/v1/webpayment', $requestData);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('✅ Orange Money: Paiement initié avec succès', $responseData);
                
                return [
                    'success' => true,
                    'data' => $responseData,
                    'payment_token' => $responseData['payment_token'] ?? null,
                    'payment_url' => $responseData['payment_url'] ?? null,
                    'message' => 'Paiement initié avec succès'
                ];
            } else {
                Log::error('❌ Orange Money: Échec initiation paiement', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                
                return [
                    'success' => false,
                    'message' => $responseData['message'] ?? 'Erreur lors de l\'initiation du paiement',
                    'error_code' => $responseData['code'] ?? 'UNKNOWN_ERROR'
                ];
            }
        } catch (Exception $e) {
            Log::error('❌ Orange Money: Erreur initiatePayment', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique lors du paiement',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
     * 
     * @param string $paymentToken - Token du paiement Orange
     * @return array - Statut du paiement
     */
    public function checkPaymentStatus($paymentToken)
    {
        try {
            $token = $this->getAccessToken();
            
            Log::info('🔍 Orange Money: Vérification statut paiement', ['token' => $paymentToken]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/orange-money-webpay/dev/v1/transactionstatus/' . $paymentToken);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('✅ Orange Money: Statut récupéré', $responseData);
                
                // Mapper les statuts Orange vers nos statuts
                $status = $this->mapOrangeStatus($responseData['status'] ?? 'UNKNOWN');
                
                return [
                    'success' => true,
                    'status' => $status,
                    'orange_status' => $responseData['status'] ?? 'UNKNOWN',
                    'data' => $responseData
                ];
            } else {
                Log::error('❌ Orange Money: Échec vérification statut', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Impossible de vérifier le statut du paiement'
                ];
            }
        } catch (Exception $e) {
            Log::error('❌ Orange Money: Erreur checkPaymentStatus', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique lors de la vérification',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mapper les statuts Orange vers nos statuts internes
     */
    private function mapOrangeStatus($orangeStatus)
    {
        $statusMap = [
            'INITIATED' => 'en_cours',
            'PENDING' => 'en_cours', 
            'SUCCESS' => 'reussi',
            'FAILED' => 'echoue',
            'CANCELLED' => 'annule',
            'EXPIRED' => 'expire'
        ];

        return $statusMap[$orangeStatus] ?? 'inconnu';
    }

    /**
     * Valider un numéro Orange Money
     */
    public function validateOrangeNumber($phoneNumber)
    {
        // Nettoyer le numéro
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Formats acceptés pour Orange Sénégal
        $patterns = [
            '/^221(77|78)[0-9]{7}$/', // Format international
            '/^(77|78)[0-9]{7}$/',    // Format local
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $phoneNumber)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Formater un numéro pour Orange Money
     */
    public function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si c'est un numéro local, ajouter le préfixe pays
        if (preg_match('/^(77|78)[0-9]{7}$/', $phoneNumber)) {
            return '221' . $phoneNumber;
        }
        
        return $phoneNumber;
    }
}
