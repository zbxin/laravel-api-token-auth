<?php

namespace ZhiEq\ApiTokenAuth;

use App\ApiTokenAuth\Guard\ApiTokenGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ApiTokenServiceProvider extends ServiceProvider
{
    /**
     * @return string
     */

    protected function configPath()
    {
        return __DIR__ . '/../config/api-token.php';
    }

    /**
     *
     */

    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('api-token.php')
        ]);
    }

    /**
     *
     */

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'api-token');
        $this->app->singleton('api-token', ApiTokenManager::class);
        $this->app->singleton(ApiTokenManager::class, function ($app) {
            return new ApiTokenManager($app['config']);
        });
        Auth::extend('api-token', function ($app, $name, array $config) {
            return new ApiTokenGuard(Auth::createUserProvider($config['provider']), $config);
        });
    }
}
