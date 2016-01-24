<?php namespace NourritureToolbox\Registrar;


use Carbon\Carbon;
use NourritureToolbox\Models\Bitrix\BUser;
use NourritureToolbox\Registrar\Events\RegistrationApplicationWasCreated;
use NourritureToolbox\Registrar\Exception\NonUniqueException;
use NourritureToolbox\Registrar\Models\RegistrationApplication;

class RegistrationExecutor
{
    private $ticketExpireHours = 24;

    /**
     * RegistrationExecutor constructor.
     */
    public function __construct()
    {
        // TODO: inject dependencies
    }

    /**
     * @param string $email
     * @throws \InvalidArgumentException
     * @throws NonUniqueException
     */
    public function handleEmail($email)
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (false === $email) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        $count = BUser::on('bitrix')->where('EMAIL', $email)->count();
        if ($count > 0) {
            throw new NonUniqueException('Email already exists');
        }

        /** @var RegistrationApplication $registrationApplication */
        if ($registrationApplication = RegistrationApplication::unexpired($email)->first()) {
            $registrationApplication->generateTicket();
        } else {
            $registrationApplication = new RegistrationApplication();
            $registrationApplication->setAttribute('email', $email);
        }

        $now = Carbon::now();
        $registrationApplication->setAttribute('expired_at', $now->addHours($this->ticketExpireHours));
        $registrationApplication->save();

        $event = new RegistrationApplicationWasCreated($registrationApplication);
        event($event);
    }

    public function expireRegistrationApplication(RegistrationApplication $registrationApplication)
    {
        $registrationApplication->setAttribute('expired_at', null);
        $registrationApplication->save();
    }

    /**
     * @param string $ticket
     * @param string $email
     * @return bool
     */
    public function validate($ticket, $email = null)
    {
        if (!$ticket) {
            throw new \InvalidArgumentException('Invalid ticket');
        }

        /** @var RegistrationApplication $registrationApplication */
        $registrationApplication = RegistrationApplication::unexpired($email, $ticket)->first();

        if ($registrationApplication) {
            $this->expireRegistrationApplication($registrationApplication);

            return true;
        }

        return false;
    }
}