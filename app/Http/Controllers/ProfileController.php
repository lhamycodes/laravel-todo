<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    /**
     * Update the specified users password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAccountPassword(Request $request){
        $messages = [
            'password_current.required' => 'Please enter your current password',
            'newPassword.required' => 'Please enter your new password',
            'newPassword.min' => 'Your new password must be 6 characters minimum',
            'newPassword.confirmed' => "The password's you entered don't match",
            'newPassword_confirmation.required' => 'Please confirm your new password',
        ];

        $validator = $this->validate($request, [
            'password_current' => 'required|min:6',
            'newPassword' => 'required|min:6|confirmed',
            'newPassword_confirmation' => 'required|min:6',
        ], $messages);

        if($validator){
            $currentPassword = Auth::user()->password;
            if(Hash::check($request->password_current, $currentPassword)){
                $userId = Auth::user()->id;
                $user = User::find($userId);
                $user->password = Hash::make($request->newPassword);

                $saveNewPassword = $user->save();
                if($saveNewPassword){
                    $output = [
                        'status' => 200,
                        'response' => [
                            "message" => "Password Updated Successfully",
                        ]
                    ];
                }
                else {
                    $output = [
                        'status' => 401,
                        'response' => [
                            "message" => "Could not Update Password",
                        ]
                    ];
                }
            }
            else {
                $output = [
                    'status' => 404,
                    'response' => [
                        "message" => "The current password you entered is wrong",
                    ]
                ];
            }
            return response()->json($output);
        }
        else {
            return redirect()->back()->withErrors($validator)->withInput();            
        }
    }

    /**
     * Update the specified users avatar in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAccountAvatar(Request $request){
        $messages = [
            'avatar_img.required' => 'Avatar image is required',
            'avatar_img.max' => 'Your avatar image has exceeded the maximum upload size of 2MB',
            'avatar_img.image' => 'The file you uploaded is not a valid image',
        ];

        $validator = $this->validate($request, [
            'avatar_img' => 'required|image|max:1999',
        ], $messages);

        if($validator){
            if($request->hasFile('avatar_img')){
                $fileName = str_slug(trim(strtolower(substr(Auth::user()->fullname, 0, 30))), '-');

                $fileExt = $request->file('avatar_img')->getClientOriginalExtension();

                $fileNameToStore = $fileName."-".time().".".$fileExt;

                $path = $request->file('avatar_img')->storeAs('public/user_avatar', $fileNameToStore);

                if($path){
                    $userId = Auth::user()->id;
                    $user = User::find($userId);
                    $user->avatar_img = $fileNameToStore;

                    $saveNewAvatar = $user->save();
                    $output = [
                        'status' => 200,
                        'response' => [
                            "message" => "Profile Avatar Successfully Uploaded",
                        ]
                    ];
                }
                else {
                    $output = [
                        'status' => 401,
                        'response' => [
                            "message" => "Could not Upload Profile Avatar",
                        ]
                    ];
                }       
            }
            return response()->json($output);
        }
        else {
            return redirect()->back()->withErrors($validator)->withInput();            
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.profile.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
