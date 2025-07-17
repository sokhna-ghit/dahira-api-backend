<?php

namespace App\Providers;
 use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Dahira;
use App\Policies\DahiraPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
        protected $policies = [

            \App\Models\Dahira::class => \App\Policies\DahiraPolicy::class,
            \App\Models\Membre::class => \App\Policies\MembrePolicy::class,
            \App\Models\Cotisation::class => \App\Policies\CotisationPolicy::class,

    ];


    /**
     * Register any authentication / authorization services.
     */
    public function boot()
{
    $this->registerPolicies();

    // Recharge automatiquement les rôles dans toutes les policies
    Gate::before(function ($user, $ability) {
        if (! $user->relationLoaded('role')) {
            $user->load('role');
        }
    });
}
}
