<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use App\Policies\BasePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Adicione seus mapeamentos de policy aqui, como planejado:
        BasePolicy::class,
        // Product::class => BasePolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Aqui é onde você pode definir "Gates" para regras de autorização
        // que não estão ligadas a um modelo específico.
        // Gate::define('edit-settings', function (User $user) {
        //     return $user->isAdmin();
        // });
    }
}
