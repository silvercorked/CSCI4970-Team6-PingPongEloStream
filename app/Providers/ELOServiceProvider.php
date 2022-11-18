<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\ELO\ELO;

class ELOServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('elo', function() {
            return new ELO();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        //
    }
}
