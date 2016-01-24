<?php namespace NourritureToolbox\Registrar\Commands;


use Illuminate\Console\Command;
use NourritureToolbox\Registrar\Models\RegistrationApplication;

class TicketCleanup extends Command
{
    protected $signature = 'registrar:ticket:cleanup';

    protected $description = 'Remove expired tickets';

    public function handle()
    {
        $deleted = RegistrationApplication::expired()->delete();

        dd($deleted);
    }
}