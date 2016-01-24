<?php
/**
 * Created by PhpStorm.
 * User: inikulin
 * Date: 22/01/16
 * Time: 22:47
 */

namespace NourritureToolbox\Registrar\Listeners;


use Illuminate\Events\Dispatcher;

class RegistrarEventListener
{
    public function onRegistrationCreate($event)
    {

    }
    
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(
            'NourritureToolbox\Registrar\Events\RegistrationApplicationWasCreated',
            'NourritureToolbox\Registrar\Listeners\RegistrarEventListener@onRegistrationCreate'
        );
    }
}