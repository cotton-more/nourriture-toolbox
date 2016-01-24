<?php namespace NourritureToolbox\Registrar\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NourritureToolbox\Registrar\Exception\NonUniqueException;
use NourritureToolbox\Registrar\RegistrationExecutor;

class DefaultController extends Controller
{
    public function createRegistration(Request $request)
    {
        $email = $request->query('email');

        /** @var RegistrationExecutor $registrationExecutor */
        $registrationExecutor = app('nour.registration_executor');

        $data = [
            'status' => 'ok',
        ];

        try {
            $registrationExecutor->handleEmail($email);
        } catch (NonUniqueException $ex) {
            $data['status'] = 'error';
            $data['error'] = $ex->getMessage();
        }

        return response()->json($data);
    }

    public function validateTicket(Request $request)
    {
        $ticket = $request->input('ticket');
        $email = $request->input('email');

        /** @var RegistrationExecutor $registrationExecutor */
        $registrationExecutor = app('nour.registration_executor');

        $result = $registrationExecutor->validate($ticket, $email);

        $data = [
            'status' => $result ? 'ok' : 'error',
            'email'  => $email,
        ];

        return response()->json($data);
    }
}