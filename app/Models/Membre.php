<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'genre',
        'date_naissance',
        'dahira_id',
        'active',
    ];



    public function cotisations()
{
    return $this->hasMany(Cotisation::class);
}

    // Relation : un Membre appartient à un Dahira
    public function dahira()
    {
        return $this->belongsTo(Dahira::class);
    }
}
