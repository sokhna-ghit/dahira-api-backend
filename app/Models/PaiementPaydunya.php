<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementPaydunya extends Model
{
    use HasFactory;

    protected $fillable = [
        'membre_id',
        'reference',
        'invoice_token',
        'invoice_url',
        'montant',
        'telephone',
        'operateur',
        'mode_paiement',
        'type_cotisation',
        'description',
        'statut',
        'statut_paydunya',
        'date_paiement',
        'donnees_paydunya',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'datetime',
        'donnees_paydunya' => 'array',
    ];

    /**
     * Relation avec le membre
     */
    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    /**
     * Scope pour filtrer par membre
     */
    public function scopeForMembre($query, $membreId)
    {
        return $query->where('membre_id', $membreId);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeWithStatus($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Obtenir les paiements récents
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
