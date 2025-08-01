<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        // Créer les rôles s'ils n'existent pas
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $memberRole = Role::firstOrCreate(['name' => 'membre']);

        // Créer un utilisateur test admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('123456'),
                'role_id' => $adminRole->id,
            ]
        );

        // Créer un utilisateur test membre
        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'User Test',
                'password' => Hash::make('123456'),
                'role_id' => $memberRole->id,
            ]
        );

        echo "Utilisateurs test créés:\n";
        echo "- admin@test.com / 123456 (admin)\n";
        echo "- user@test.com / 123456 (membre)\n";
    }
}
