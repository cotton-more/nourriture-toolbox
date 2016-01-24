<?php
/**
 * Created by PhpStorm.
 * User: inikulin
 * Date: 22/01/16
 * Time: 22:47
 */

namespace NourritureToolbox\Registrar\Listeners;


use Illuminate\Events\Dispatcher;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use NourritureToolbox\Registrar\Events\RegistrationApplicationWasCreated;

class RegistrarEventListener
{
    public function onRegistrationCreate(RegistrationApplicationWasCreated $event)
    {
        $registrationApplication = $event->getRegistrationApplication();
        $email = $registrationApplication->getAttribute('email');
        $ticket = $registrationApplication->getAttribute('ticket');

        /** @var Mailer $mailer */
        $mailer = app('mailer');

        try {
            $mailer->send('registrar::emails.confirm_email', [
                'email' => $email,
                'ticket' => $ticket,
            ], function (Message $message) use ($email) {
                $message->to($email);
                $message->from('registrar@nourriture.ru');
            });
        } catch (\Exception $ex) {
            //
        }
    }
    
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(
            'NourritureToolbox\Registrar\Events\RegistrationApplicationWasCreated',
            'NourritureToolbox\Registrar\Listeners\RegistrarEventListener@onRegistrationCreate'
        );
    }
}