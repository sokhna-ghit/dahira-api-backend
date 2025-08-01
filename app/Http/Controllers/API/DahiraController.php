<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dahira;

class DahiraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            \Log::info('📋 DahiraController->index() appelée');
            $dahiras = Dahira::with('membres')->get();
            \Log::info('✅ Dahiras trouvées: ' . $dahiras->count());
            return response()->json($dahiras);
        } catch (\Exception $e) {
            \Log::error('❌ Erreur dans DahiraController->index(): ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des dahiras'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('🆕 DahiraController->store() appelée avec données: ' . json_encode($request->all()));
            
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'ville' => 'nullable|string|max:255',
                'region' => 'nullable|string|max:255',
                'adresse' => 'nullable|string|max:500',
                'confrerie' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'imageUrl' => 'nullable|string|max:500',
                'nombreMembres' => 'nullable|integer|min:0',
                'statut' => 'nullable|string|in:actif,inactif',
            ]);

            $validated['statut'] = $validated['statut'] ?? 'actif';
            
            $dahira = Dahira::create($validated);
            \Log::info('✅ Dahira créée avec ID: ' . $dahira->id);
            
            return response()->json($dahira, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Erreur de validation: ' . json_encode($e->errors()));
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('❌ Erreur dans DahiraController->store(): ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création du dahira'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Dahira::with('membres')->findOrFail($id);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dahira = Dahira::findOrFail($id);
        $dahira->update($request->all());
        return $dahira;
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         Dahira::findOrFail($id)->delete();
        return response()->json(['message' => 'Dahira supprimé']);
    }
}
