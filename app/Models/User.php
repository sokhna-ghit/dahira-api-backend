<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ✅ inclus pour qu’on puisse l’utiliser à l'inscription
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
    ];

    public function role()
{
    return $this->belongsTo(Role::class);
}


}
