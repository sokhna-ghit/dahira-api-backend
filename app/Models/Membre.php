<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'genre',
        'date_naissance',
        'profession',
        'statut',
        'commentaires',
        'date_inscription',
        'dahira_id',
        'user_id',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_inscription' => 'date',
    ];

    // Relation : un Membre appartient à un Dahira
    public function dahira()
    {
        return $this->belongsTo(Dahira::class);
    }

    // Relation : un Membre peut être lié à un User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation : un Membre peut avoir plusieurs cotisations
    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeInactifs($query)
    {
        return $query->where('statut', 'inactif');
    }

    public function scopeSuspendus($query)
    {
        return $query->where('statut', 'suspendu');
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getEstActifAttribute()
    {
        return $this->statut === 'actif';
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }
}
