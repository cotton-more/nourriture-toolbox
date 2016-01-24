<?php namespace NourritureToolbox\Registrar;


use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RegistrarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'registrar');

        /** @var Dispatcher $events */
        $events = $this->app['events'];

        $events->subscribe('NourritureToolbox\Registrar\Listeners\RegistrarEventListener');

        /** @var Router $router */
        $router = $this->app['router'];

        $routeConfig = [
            'as' => 'registrar::',
            'namespace' => 'NourritureToolbox\Registrar\Controllers',
            'prefix' => 'nour_registrar',
        ];
        $router->group($routeConfig, function (Router $router) {
            $router->get('create', [
                'as' => 'create_registration',
                'uses' => 'DefaultController@createRegistration',
            ]);
            $router->get('validate', [
                'as' => 'validate_ticket',
                'uses' => 'DefaultController@validateTicket',
            ]);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /** @var Application $app */
        $app = $this->app;

        $migrations = realpath(__DIR__.'/migrations');
        $this->publishes([
            $migrations => $app->databasePath().'/migrations',
        ], 'migrations');

        $app->bind('nour.registration_executor', function () {
            return new RegistrationExecutor;
        });

        $app->singleton('command.nour.ticket_cleanup', function () {
            return new Commands\TicketCleanup();
        });

        $this->commands([
            'command.nour.ticket_cleanup',
        ]);
    }

    public function provides()
    {
        return [
            'nour.registration_executor',
            'command.nour.ticket_cleanup',
        ];
    }
}