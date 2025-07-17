<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membre;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class MembreController extends Controller
{
    /**
     * Afficher la liste des membres.
     */
    public function index()
    {
        // $this->authorize('viewAny', Membre::class);
        return Membre::all();
    }

    /**
     * Créer un nouveau membre.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'email' => 'required|email|unique:membres',
        'telephone' => 'required|string',
        'adresse' => 'required|string',
        'genre' => 'required|in:masculin,féminin',
        'date_naissance' => 'required|date',
        'dahira_id' => 'required|exists:dahiras,id',
    ]);

    $membre = Membre::create($validated);

    return response()->json($membre, 201);
}

    /**
     * Afficher un membre spécifique.
     */
    public function show(string $id)
    {
        $membre = Membre::with('dahira')->findOrFail($id);
        // $this->authorize('view', $membre);

        return response()->json($membre);
    }

    /**
     * Modifier un membre.
     */
    public function update(Request $request, string $id)
    {
        $membre = Membre::findOrFail($id);
        // $this->authorize('update', $membre);

        $membre->update($request->only(['name', 'email', 'role']));
        return response()->json($membre);
    }

    /**
     * Supprimer un membre.
     */
    public function destroy(string $id)
    {
        $membre = Membre::findOrFail($id);
        // $this->authorize('delete', $membre);

        $membre->delete();
        return response()->json(['message' => 'Membre supprimé']);
    }
}
