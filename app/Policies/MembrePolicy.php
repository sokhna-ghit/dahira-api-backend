<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Membre;

class MembrePolicy
{
    public function viewAny(User $user): bool
    {
        // Seuls admin, président et trésorier peuvent voir la liste des membres
        return in_array($user->role->name, ['admin', 'président', 'trésorier']);
    }

    public function view(User $user, Membre $membre): bool
    {
        // Même logique : ils peuvent voir un membre spécifique
        return in_array($user->role->name, ['admin', 'président', 'trésorier']);
    }

    public function create(User $user): bool
    {
        // Admin et président peuvent ajouter un membre
        return in_array($user->role->name, ['admin', 'président']);
    }

    public function update(User $user, Membre $membre): bool
    {
        // Admin peut tout modifier, Président peut modifier les membres de son dahira
        if ($user->role->name === 'admin') {
            return true;
        }

        if ($user->role->name === 'président') {
            return $user->dahira_id === $membre->dahira_id;
        }

        return false;
    }

    public function delete(User $user, Membre $membre): bool
    {
        // Seul admin peut supprimer
        return $user->role->name === 'admin';
    }
}
