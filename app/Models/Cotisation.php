<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'membre_id',
        'dahira_id',
        'montant',
        'type',
        'date_paiement',
        'paiement_id',
        'statut',
        'description',
        'periode', // Pour cotisations périodiques
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
        'montant' => 'decimal:2',
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

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // Scopes
    public function scopePaye($query)
    {
        return $query->where('statut', 'paye');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePourMois($query, $annee, $mois)
    {
        return $query->whereYear('date_paiement', $annee)
                    ->whereMonth('date_paiement', $mois);
    }

    public function scopePourAnnee($query, $annee)
    {
        return $query->whereYear('date_paiement', $annee);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Méthodes utilitaires
    public function estPayee()
    {
        return $this->statut === 'paye';
    }

    public function getMontantFormate()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }
}
