<?php
/**
 * Created by PhpStorm.
 * User: inikulin
 * Date: 22/01/16
 * Time: 22:16
 */

namespace NourritureToolbox\Registrar\Events;


use NourritureToolbox\Registrar\Models\RegistrationApplication;

class RegistrationApplicationWasCreated
{
    /**
     * @var RegistrationApplication
     */
    private $registrationApplication;

    public function __construct(RegistrationApplication $registrationApplication)
    {
        $this->registrationApplication = $registrationApplication;
    }

    /**
     * @return RegistrationApplication
     */
    public function getRegistrationApplication()
    {
        return $this->registrationApplication;
    }
}