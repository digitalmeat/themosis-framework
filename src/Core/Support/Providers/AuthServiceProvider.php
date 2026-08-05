<?php

namespace Themosis\Core\Support\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    /**
     * Register the application's policies.
     *
     * The framework now invokes this method automatically (Laravel 10),
     * so subclasses no longer need to call it manually from their `boot` method.
     */
    public function registerPolicies()
    {
        foreach ($this->policies() as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    public function register()
    {
        $this->booting(function () {
            $this->registerPolicies();
        });
    }

    /**
     * Return the policies defined by the provider.
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }
}
