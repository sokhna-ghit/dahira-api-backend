<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dahira extends Model
{
    protected $fillable = [
        'nom',
        'ville',
        'region',
        'adresse',
        'confrerie',
        'description',
        'imageUrl',
        'nombreMembres',
        'statut',
    ];

    // Relation : un Dahira a plusieurs membres
    public function membres()
    {
        return $this->hasMany(Membre::class);
    }
}
