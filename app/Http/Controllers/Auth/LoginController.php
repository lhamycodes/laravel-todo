<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $rules = array(
            'email' => 'required|email|min:5',
            'password' => 'required|min:6',
        );

        $validator = $this->validate($request, $rules);

        $userData = array(
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        );

        if (!$validator) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            if (Auth::attempt($userData)) {
                $redirectTo = "dashboard";

                $output = array(
                    'status' => 200,
                    'response' => [
                        "message" => "Login Successful",
                        "redirectTo" => $redirectTo,
                    ],
                );
            } else {
                $output = array(
                    'status' => 404,
                    'response' => "Invalid Login Credentials",
                );
            }
        }
        return response()->json($output);
    }
}
