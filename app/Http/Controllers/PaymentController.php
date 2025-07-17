<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Paiement;

class PaymentController extends Controller
{
    public function simulatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'phone' => 'required|string',
        ]);

        $reference = uniqid('sim_');
        $isSuccess = rand(1, 100) <= 80;
        $status = $isSuccess ? 'success' : 'failed';

        $paiement = Paiement::create([
            'phone' => $request->phone,
            'amount' => $request->amount,
            'status' => $status,
            'reference' => $reference,
        ]);

        return response()->json([
            'status' => $status,
            'reference' => $reference,
            'message' => $isSuccess ? 'Paiement réussi.' : 'Paiement échoué.',
            'paiement_id' => $paiement->id,
        ]);
    }

    public function genererRecu($id)
    {
        $paiement = Paiement::findOrFail($id);

        $pdf = Pdf::loadView('recu', [
            'paiement' => $paiement,
            'dahira' => 'Dahira Sokhna',
        ]);

        return $pdf->download('recu_'.$paiement->reference.'.pdf');
    }
}
