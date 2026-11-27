<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin passes every Policy check. Return null (not false) for everyone else,
        // so their own Policy still decides.
        Gate::before(fn (User $user) => $user->role === 'admin' ? true : null);
    }
}
