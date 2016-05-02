<?php namespace Infinety\Token;

use Illuminate\Support\ServiceProvider;

class TokenServiceProvider extends ServiceProvider
{
    protected $defer = false;
    protected $commands = [
        AddMigrationCommand::class,
    ];
    public function register()
    {
        $this->commands($this->commands);
    }
    public function boot()
    {
        //
    }
}