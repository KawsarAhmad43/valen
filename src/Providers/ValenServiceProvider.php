<?php

namespace Kawsarahmad\Valen\Providers;

use Illuminate\Support\ServiceProvider;

use Kawsarahmad\Valen\Valen;

class ValenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('valen', function(){
            return new Valen();
        });
    }
    public function boot()
    {

    }
}
