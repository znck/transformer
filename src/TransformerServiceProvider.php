<?php namespace Znck\Repositories;

use Illuminate\Support\ServiceProvider;
use Znck\Transformers\Console\TransformerMakeCommand;

class TransformerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('command.make.transformer', TransformerMakeCommand::class);
        $this->commands('command.make.transformer');
    }

    public function provides() {
        return ['command.make.transformer'];
    }
}
