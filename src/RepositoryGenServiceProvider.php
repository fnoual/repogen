<?php

namespace Fnoual\Generators;

use Fnoual\Generators\Commands\GenerateRepositoryCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class RepositoryGenServiceProvider extends BaseServiceProvider {


    public function boot()
    {
        $this->commands([
            GenerateRepositoryCommand::class,
        ]);
    }

    public function register()
    {

    }

}
