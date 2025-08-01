<?php

namespace App\Services;

use App\Models\Paiement;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class PdfService
{
    public function genererRecuPaiement(Paiement $paiement): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->getHtmlTemplate($paiement);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'recu_paiement_' . $paiement->reference_transaction . '.pdf';
        $output = $dompdf->output();
        
        // Sauvegarder le fichier
        $path = storage_path('app/public/recu/' . $filename);
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $output);
        
        return $filename;
    }
    
    private function getHtmlTemplate(Paiement $paiement): string
    {
        $membre = $paiement->membre;
        $dahira = $paiement->dahira;
        $dateCreation = Carbon::parse($paiement->created_at);
        $datePaiement = $paiement->date_paiement ? Carbon::parse($paiement->date_paiement) : $dateCreation;
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Reçu de Paiement</title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    margin: 0;
                    padding: 20px;
                    color: #333;
                    line-height: 1.4;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border: 2px solid #e67e22;
                    border-radius: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 3px solid #e67e22;
                    padding-bottom: 20px;
                }
                .header h1 {
                    color: #e67e22;
                    margin: 0;
                    font-size: 28px;
                    font-weight: bold;
                }
                .header p {
                    color: #666;
                    margin: 5px 0;
                    font-size: 14px;
                }
                .recu-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                    border-left: 4px solid #e67e22;
                }
                .recu-info h2 {
                    color: #e67e22;
                    margin: 0 0 15px 0;
                    font-size: 20px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    margin: 8px 0;
                    padding: 5px 0;
                    border-bottom: 1px dotted #ddd;
                }
                .info-row:last-child {
                    border-bottom: none;
                }
                .info-label {
                    font-weight: bold;
                    color: #555;
                    width: 40%;
                }
                .info-value {
                    color: #333;
                    width: 55%;
                    text-align: right;
                }
                .montant-section {
                    background: #e67e22;
                    color: white;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: center;
                    margin: 20px 0;
                }
                .montant-section h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: normal;
                }
                .montant-section .montant {
                    font-size: 32px;
                    font-weight: bold;
                    margin: 10px 0;
                }
                .statut {
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .statut.reussi {
                    background: #27ae60;
                    color: white;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    border-top: 1px solid #eee;
                    padding-top: 20px;
                }
                .qr-section {
                    text-align: center;
                    margin: 20px 0;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }
                .operateur {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .operateur.orange { background: #ff6600; color: white; }
                .operateur.free { background: #00bfff; color: white; }
                .operateur.wave { background: #1e88e5; color: white; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🕌 REÇU DE PAIEMENT</h1>
                    <p><strong>" . ($dahira ? $dahira->nom : 'Dahira') . "</strong></p>
                    <p>Système de Gestion des Cotisations</p>
                </div>
                
                <div class='recu-info'>
                    <h2>📋 Informations du Paiement</h2>
                    <div class='info-row'>
                        <span class='info-label'>Référence:</span>
                        <span class='info-value'><strong>{$paiement->reference_transaction}</strong></span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Date du paiement:</span>
                        <span class='info-value'>{$datePaiement->format('d/m/Y à H:i')}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Statut:</span>
                        <span class='info-value'>
                            <span class='statut reussi'>" . ucfirst($paiement->statut) . "</span>
                        </span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Opérateur:</span>
                        <span class='info-value'>
                            <span class='operateur " . strtolower($paiement->operateur) . "'>" . ucfirst($paiement->operateur) . " Money</span>
                        </span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Type de cotisation:</span>
                        <span class='info-value'>" . ucfirst(str_replace('_', ' ', $paiement->type_cotisation)) . "</span>
                    </div>
                </div>
                
                <div class='montant-section'>
                    <h3>Montant Payé</h3>
                    <div class='montant'>" . number_format($paiement->montant, 0, ',', '.') . " FCFA</div>
                </div>
                
                <div class='recu-info'>
                    <h2>👤 Informations du Membre</h2>
                    <div class='info-row'>
                        <span class='info-label'>Nom complet:</span>
                        <span class='info-value'>" . ($membre ? $membre->name : 'N/A') . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Email:</span>
                        <span class='info-value'>" . ($membre ? $membre->email : 'N/A') . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Téléphone:</span>
                        <span class='info-value'>{$paiement->telephone}</span>
                    </div>
                    " . ($paiement->description ? "
                    <div class='info-row'>
                        <span class='info-label'>Description:</span>
                        <span class='info-value'>{$paiement->description}</span>
                    </div>
                    " : "") . "
                </div>
                
                <div class='qr-section'>
                    <p><strong>🔍 Vérification</strong></p>
                    <p>Ce reçu peut être vérifié en utilisant la référence: <strong>{$paiement->reference_transaction}</strong></p>
                </div>
                
                <div class='footer'>
                    <p><strong>Merci pour votre contribution ! 🙏</strong></p>
                    <p>Ce reçu a été généré automatiquement le " . now()->format('d/m/Y à H:i') . "</p>
                    <p>Pour toute question, contactez l'administration de votre Dahira</p>
                    <p style='font-size: 10px; margin-top: 15px;'>
                        DahiraApp © " . date('Y') . " - Système de Gestion de Dahira
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
