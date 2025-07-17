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
        $this->authorize('viewAny', Dahira::class);
        return Dahira::with('membres')->get(); 
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)

    {
        $this->authorize('create', Dahira::class);

        $validated = $request->validate([

            'nom' => 'required|string',
            'ville' => 'nullable|string',
        ]);

        return Dahira::create($validated);
        
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
