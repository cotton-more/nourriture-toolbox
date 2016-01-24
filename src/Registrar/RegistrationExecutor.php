<?php namespace NourritureToolbox\Registrar;


use Carbon\Carbon;
use NourritureToolbox\Models\Bitrix\BUser;
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
     * @throws NonUniqueException
     */
    public function handleEmail($email)
    {
        $count = BUser::where('EMAIL', $email)->count();
        if ($count > 0) {
            throw new NonUniqueException('Email already exists');
        }

        $now = Carbon::now();
        $registrationApplication = new RegistrationApplication();
        $registrationApplication->setAttribute('email', $email);
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
        /** @var RegistrationApplication $registrationApplication */
        $registrationApplication = RegistrationApplication::unexpired($ticket, $email)->first();

        if ($registrationApplication) {
            $this->expireRegistrationApplication($registrationApplication);

            return true;
        }

        return false;
    }
}