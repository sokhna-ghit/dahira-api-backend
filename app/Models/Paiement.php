<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'membre_id',
        'dahira_id',
        'montant',
        'telephone',
        'operateur',
        'type_cotisation',
        'description',
        'reference',
        'statut',
        'method_paiement',
        'transaction_id',
        'date_paiement',
        // Anciens champs pour compatibilité
        'phone',
        'amount',
        'status',
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
        'montant' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relations
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function dahira()
    {
        return $this->belongsTo(Dahira::class);
    }

    public function cotisation()
    {
        return $this->hasOne(Cotisation::class);
    }

    // Accesseurs pour compatibilité
    public function getAmountAttribute()
    {
        return $this->montant;
    }

    public function getPhoneAttribute()
    {
        return $this->telephone;
    }

    public function getStatusAttribute()
    {
        return $this->statut;
    }

    // Scopes
    public function scopeReussis($query)
    {
        return $query->where('statut', 'reussi');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEchoues($query)
    {
        return $query->where('statut', 'echoue');
    }

    public function scopePourDahira($query, $dahiraId)
    {
        return $query->where('dahira_id', $dahiraId);
    }

    public function scopePourMembre($query, $membreId)
    {
        return $query->where('membre_id', $membreId);
    }
}
