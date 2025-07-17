<?php

namespace App\Mail;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class RecuPaiementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paiement;

    public function __construct(Paiement $paiement)
    {
        $this->paiement = $paiement;
    }

    public function build()
    {
        $pdf = Pdf::loadView('recu', [
            'paiement' => $this->paiement,
            'dahira' => 'Dahira Sokhna'
        ]);

        return $this->subject('Votre reçu de paiement')
                    ->view('emails.recu') // cette vue est requise
                    ->attachData($pdf->output(), 'recu_'.$this->paiement->reference.'.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
