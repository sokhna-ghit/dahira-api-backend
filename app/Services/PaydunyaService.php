<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class PaydunyaService
{
    private $masterKey;
    private $privateKey;
    private $publicKey;
    private $token;
    private $mode;
    private $baseUrl;

    public function __construct()
    {
        $this->masterKey = config('services.paydunya.master_key');
        $this->privateKey = config('services.paydunya.private_key');
        $this->publicKey = config('services.paydunya.public_key');
        $this->token = config('services.paydunya.token');
        $this->mode = config('services.paydunya.mode');
        $this->baseUrl = config('services.paydunya.base_url');
        
        Log::info('🔧 PayDunya Service initialisé', [
            'mode' => $this->mode,
            'base_url' => $this->baseUrl
        ]);
    }

    /**
     * 🎯 Initier un paiement avec PayDunya
     * 
     * Cette méthode gère TOUS les opérateurs :
     * - Orange Money (77, 78)
     * - Free Money (70, 76) 
     * - Wave (33)
     * - Cartes bancaires
     * 
     * PayDunya détecte automatiquement l'opérateur selon le numéro !
     */
    public function initiatePayment($paymentData)
    {
        try {
            Log::info('💰 PayDunya: Initiation paiement', $paymentData);

            // Détecter automatiquement l'opérateur selon le numéro
            $operateur = $this->detectOperator($paymentData['telephone']);
            
            // Préparer les données pour PayDunya
            $requestData = [
                'invoice' => [
                    'total_amount' => (float) $paymentData['montant'],
                    'description' => $paymentData['description'] ?? 'Cotisation Dahira - ' . $paymentData['type_cotisation'],
                ],
                'store' => [
                    'name' => 'Dahira Management System',
                    'tagline' => 'Système de gestion des cotisations',
                    'phone' => '+221701234567', // Votre numéro
                    'postal_address' => 'Dakar, Sénégal',
                    'website_url' => config('app.url'),
                ],
                'custom_data' => [
                    'membre_id' => $paymentData['membre_id'],
                    'reference' => $paymentData['reference'],
                    'type_cotisation' => $paymentData['type_cotisation'],
                    'operateur_detecte' => $operateur,
                ],
                'actions' => [
                    'cancel_url' => config('app.url') . '/payment/cancel',
                    'return_url' => config('app.url') . '/payment/success',
                    'callback_url' => config('app.url') . '/api/paydunya/callback',
                ],
                // Spécifier le mode de paiement selon l'opérateur
                'mode' => $this->getPaymentMode($operateur, $paymentData['telephone'])
            ];

            // Headers PayDunya
            $headers = [
                'PAYDUNYA-MASTER-KEY' => $this->masterKey,
                'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
                'PAYDUNYA-TOKEN' => $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            Log::info('🚀 PayDunya: Envoi requête API', [
                'url' => $this->baseUrl . '/v1/checkout-invoice/create',
                'headers' => array_keys($headers), // Log keys only for security
                'data' => $requestData
            ]);

            // Appel API PayDunya avec l'endpoint correct
            $response = Http::withHeaders($headers)
                ->timeout(30) // Timeout de 30 secondes
                ->post($this->baseUrl . '/v1/checkout-invoice/create', $requestData);

            Log::info('💰 PayDunya paiement response status: ' . $response->status());
            Log::info('💰 PayDunya paiement response body: ' . $response->body());

            $responseData = $response->json();

            // Vérifier si c'est une erreur Cloudflare (403/500) ou une page HTML
            if ($response->status() >= 400 && str_contains($response->body(), 'cloudflare')) {
                Log::warning('🔄 PayDunya temporairement bloqué par Cloudflare, fallback vers simulation');
                
                return [
                    'success' => false,
                    'message' => 'Service PayDunya temporairement indisponible',
                    'error_code' => 'CLOUDFLARE_BLOCK',
                    'fallback_needed' => true
                ];
            }

            if ($response->successful() && isset($responseData['response_code']) && $responseData['response_code'] == '00') {
                Log::info('✅ PayDunya: Paiement initié avec succès', $responseData);
                
                return [
                    'success' => true,
                    'data' => $responseData,
                    'invoice_token' => $responseData['token'] ?? null,
                    'invoice_url' => $responseData['response_text'] ?? null,
                    'operateur' => $operateur,
                    'mode_paiement' => $this->getPaymentMode($operateur, $paymentData['telephone']),
                    'message' => 'Paiement initié avec succès via ' . $operateur
                ];
            } else {
                Log::error('❌ PayDunya: Échec initiation paiement', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                
                return [
                    'success' => false,
                    'message' => $responseData['response_text'] ?? 'Erreur lors de l\'initiation du paiement',
                    'error_code' => $responseData['response_code'] ?? 'UNKNOWN_ERROR'
                ];
            }
        } catch (Exception $e) {
            Log::error('❌ PayDunya: Erreur initiatePayment', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique lors du paiement',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 🔍 Vérifier le statut d'un paiement PayDunya
     */
    public function checkPaymentStatus($invoiceToken)
    {
        try {
            Log::info('🔍 PayDunya: Vérification statut', ['token' => $invoiceToken]);

            $headers = [
                'PAYDUNYA-MASTER-KEY' => $this->masterKey,
                'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
                'PAYDUNYA-TOKEN' => $this->token,
                'Accept' => 'application/json',
            ];

            $response = Http::withHeaders($headers)
                ->get($this->baseUrl . '/v1/checkout-invoice/confirm/' . $invoiceToken);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('✅ PayDunya: Statut récupéré', $responseData);
                
                // Mapper les statuts PayDunya vers nos statuts
                $status = $this->mapPaydunyaStatus($responseData['status'] ?? 'unknown');
                
                return [
                    'success' => true,
                    'status' => $status,
                    'paydunya_status' => $responseData['status'] ?? 'unknown',
                    'data' => $responseData,
                    'transaction_id' => $responseData['custom_data']['transaction_id'] ?? null,
                ];
            } else {
                Log::error('❌ PayDunya: Échec vérification statut', [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Impossible de vérifier le statut du paiement'
                ];
            }
        } catch (Exception $e) {
            Log::error('❌ PayDunya: Erreur checkPaymentStatus', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique lors de la vérification',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 🤖 Détecter automatiquement l'opérateur selon le numéro
     */
    public function detectOperator($phoneNumber)
    {
        // Nettoyer le numéro
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Détecter selon les préfixes sénégalais
        if (preg_match('/^(?:221)?(77|78)/', $phone)) {
            return 'Orange Money';
        }
        
        if (preg_match('/^(?:221)?(70|76)/', $phone)) {
            return 'Free Money';
        }
        
        if (preg_match('/^(?:221)?33/', $phone)) {
            return 'Wave';
        }
        
        // Par défaut (Wave supporte tous les réseaux)
        return 'Wave';
    }

    /**
     * 🎯 Obtenir le mode de paiement PayDunya selon l'opérateur
     */
    private function getPaymentMode($operateur, $phoneNumber)
    {
        switch ($operateur) {
            case 'Orange Money':
                return 'orange-money-senegal';
                
            case 'Free Money':
                return 'tigo-senegal'; // Free Money utilise le réseau Tigo
                
            case 'Wave':
                return 'wave-senegal';
                
            default:
                return 'mobile-money'; // Mode générique
        }
    }

    /**
     * 🔄 Mapper les statuts PayDunya vers nos statuts internes
     */
    private function mapPaydunyaStatus($paydunyaStatus)
    {
        $statusMap = [
            'pending' => 'en_cours',
            'completed' => 'reussi',
            'cancelled' => 'annule',
            'failed' => 'echoue',
            'expired' => 'expire'
        ];

        return $statusMap[strtolower($paydunyaStatus)] ?? 'inconnu';
    }

    /**
     * ✅ Valider un numéro de téléphone sénégalais
     */
    public function validatePhoneNumber($phoneNumber)
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Formats acceptés pour le Sénégal
        $patterns = [
            '/^221(77|78|70|76|33)[0-9]{7}$/', // Format international
            '/^(77|78|70|76|33)[0-9]{7}$/',    // Format local
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $phone)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 📱 Formater un numéro pour les APIs
     */
    public function formatPhoneNumber($phoneNumber)
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si c'est un numéro local, ajouter le préfixe pays
        if (preg_match('/^(77|78|70|76|33)[0-9]{7}$/', $phone)) {
            return '221' . $phone;
        }
        
        return $phone;
    }

    /**
     * 📊 Obtenir les opérateurs supportés
     */
    public function getSupportedOperators()
    {
        return [
            [
                'code' => 'orange',
                'nom' => 'Orange Money',
                'prefixes' => '77, 78',
                'couleur' => '#FF6600',
                'logo' => '🟠',
                'paydunya_mode' => 'orange-money-senegal'
            ],
            [
                'code' => 'free',
                'nom' => 'Free Money',
                'prefixes' => '70, 76',
                'couleur' => '#00AAFF',
                'logo' => '🔵',
                'paydunya_mode' => 'tigo-senegal'
            ],
            [
                'code' => 'wave',
                'nom' => 'Wave',
                'prefixes' => '33 + tous réseaux',
                'couleur' => '#00D4AA',
                'logo' => '🟢',
                'paydunya_mode' => 'wave-senegal'
            ],
            [
                'code' => 'card',
                'nom' => 'Carte Bancaire',
                'prefixes' => 'Visa, Mastercard',
                'couleur' => '#6C5CE7',
                'logo' => '💳',
                'paydunya_mode' => 'card'
            ]
        ];
    }
}
