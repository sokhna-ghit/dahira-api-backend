<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cotisation;
use Illuminate\Http\Request;

class CotisationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Cotisation::class, 'cotisation');
    }

    public function index()
{
    return Cotisation::with('membre')->get();
}

public function store(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|string',
        'montant' => 'required|numeric',
        'date_paiement' => 'required|date',
        'membre_id' => 'required|exists:membres,id',
    ]);

    return Cotisation::create($validated);
}


    public function show(Cotisation $cotisation)
    {
        return $cotisation;
    }

    public function update(Request $request, Cotisation $cotisation)
    {
        $this->authorize('update', $cotisation);

        $validated = $request->validate([
            'membre_id' => 'exists:membres,id',
            'dahira_id' => 'exists:dahiras,id',
            'montant' => 'numeric|min:0',
            'date_paiement' => 'date',
        ]);

        $cotisation->update($validated);

        return $cotisation;
    }

    public function destroy(Cotisation $cotisation)
    {
        $this->authorize('delete', $cotisation);

        $cotisation->delete();

        return response()->json(['message' => 'Cotisation supprimée']);
    }
}
