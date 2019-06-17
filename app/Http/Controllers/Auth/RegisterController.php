<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use \Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fullname' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);
    }

    public function register(Request $request)
    {

        $redirectTo = "/dashboard";

        $__data = $request->only('fullname', 'email', 'password');

        //Validates data
        $validator = $this->validator($__data)->validate();

        if($validator){
            //Create user
            $user = $this->create($request->all());

            if ($user) {
                //Authenticates user
                $this->guard()->login($user);
                $res = [
                    "status" => 200,
                    "response" => [
                        "message" => "Registration successful",
                        "redirectTo" => $redirectTo,
                    ],
                ]; 
                return response()->json($res);
            } else {
                $res = [
                    "status" => 400,
                    "response" => [
                        "message" => "Somewhere, somehow, something went wrong",
                        "redirectTo" => null,
                    ],
                ];
                return response()->json($res);
            }
        }
        else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        do {
            $code = Str::orderedUuid();
            $code_avail = User::where('uuid', $code)->first();
        } while (!empty($code_avail));

        try{
            $user = User::create([
                'uuid' => $code,
                'fullname' => $data['fullname'],
                'email' => $data['email'],
                'username' => str_slug($data['fullname'], '-'),
                'password' => Hash::make($data['password']),
            ]);

            return $user;
        } 
        catch (\Exception $e) {
            return $e;
        }
    }
}
