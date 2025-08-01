<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'dahira_id',
        'email_verified_at',
        'is_approved',
        'approved_at',
        'approved_by',
        'approval_notes',
        'status',
    ];

    /**
     * Attributs masqués pour les tableaux (ex. JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les castings des attributs.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    /**
     * Relation avec le modèle Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relation avec le modèle Dahira
     */
    public function dahira()
    {
        return $this->belongsTo(Dahira::class);
    }

    /**
     * Relation avec le modèle Membre
     */
    public function membre()
    {
        return $this->hasOne(Membre::class);
    }

    /**
     * Utilisateur qui a approuvé ce compte
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Comptes approuvés par cet utilisateur
     */
    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Vérifier si l'utilisateur est approuvé
     */
    public function isApproved()
    {
        return $this->is_approved && $this->status === 'approved';
    }

    /**
     * Vérifier si l'utilisateur est en attente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
