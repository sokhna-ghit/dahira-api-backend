<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cotisation;

class CotisationPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'trésorier']);
    }

    public function view(User $user, Cotisation $cotisation): bool
    {
        return $user->role->name === 'admin' || 
               ($user->role->name === 'trésorier' && $user->dahira_id === $cotisation->dahira_id);
    }

    public function create(User $user): bool
    {
        //  dd($user->role->name);
        return in_array($user->role->name, ['admin', 'trésorier']);
    }

    public function update(User $user, Cotisation $cotisation): bool
    {
        return $user->role->name === 'admin' || 
               ($user->role->name === 'trésorier' && $user->dahira_id === $cotisation->dahira_id);
    }

    public function delete(User $user, Cotisation $cotisation): bool
    {
        return $user->role->name === 'admin';
    }
}
