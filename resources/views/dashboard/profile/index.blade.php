@extends('layouts.dashboard.main')

@section('title', 'Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profile</li>
@endsection

@section('page-content')
    <div class="mt-4 col-md-8" style="margin:0px auto">
        <ul class="nav nav-tabs nav-fill" style="border-radius:0px">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">My Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Password</a>
            </li>
        </ul>
        <div class="card" style="border-radius:0px">
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" role="tabpanel" id="profile" aria-labelledby="profile-tab">
                        <div class="media mb-4">
                            <img alt="Image" src="{{ asset('/storage/user_avatar/'.Auth::user()->avatar_img.'') }}" class="avatar avatar-lg" />
                            <form id="updateAvatarForm" action="{{ route('profile.avatar-update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="media-body ml-3">
                                    <div class="custom-file custom-file-naked d-block mb-1">
                                        <input type="file" name="avatar_img" class="custom-file-input d-none" id="avatar-file">
                                        <label class="custom-file-label position-relative" for="avatar-file">
                                            <span class="btn btn-primary" id="updateAvatarBtn">
                                                Change avatar
                                            </span>
                                        </label>
                                    </div>
                                    <small style="color:red">We advice you use an image not more than 2MB in .jpg or .png format</small>
                                </div>
                            </form>
                        </div>
                        <div>
                            <div class="form-group row align-items-center">
                                <label class="col-3">Full Name : </label>
                                <div class="col">
                                    <input type="text" value="{{ Auth::user()->fullname }}" class="form-control" disabled/>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-3">Email Address : </label>
                                <div class="col">
                                    <input type="text" value="{{ Auth::user()->email }}" class="form-control" disabled/>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-3">Username : </label>
                                <div class="col">
                                    <input type="text" value="{{ '@'.Auth::user()->username }}" class="form-control" disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" role="tabpanel" id="password" aria-labelledby="password-tab">
                        <form id="updateAccountPasswordForm" method="POST" action="{{ route('profile.password-update') }}">
                            @csrf
                            <div class="form-group row align-items-center">
                                <label class="col-3">Current Password</label>
                                <div class="col">
                                    <input type="password" placeholder="Enter your current password" name="password_current" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-3">New Password</label>
                                <div class="col">
                                    <input type="password" placeholder="Enter new password" name="newPassword" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-3">Confirm Password</label>
                                <div class="col">
                                    <input type="password" placeholder="Confirm new password" name="newPassword_confirmation" class="form-control" required/>
                                </div>
                            </div>
                            <div class="text-center text-danger pb-2">
                                    <small>New password should be at least 6 characters</small>
                                </div>
                            <div class="row justify-content-end">
                                <button type="submit" class="btn btn-primary" id="updateAccountPasswordBtn">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('runJavascript')
    <script type="text/javascript">
        // Update User Password
        $('#updateAccountPasswordForm').submit(function(event){
            event.preventDefault();

            formAction = $(this).attr('action');
            formID = $(this).attr('id');
            formMethod = $(this).attr('method');
            btnHandler = $("button[type=submit]", this).attr('id');
            dataToSend = $(this).serialize();
            
            var promise = postData(formAction, dataToSend, btnHandler);

            promise.then(data => {
                console.log(data)
                if(data.status == 200){
                    notify("success", data.response.message);
                    $('#'+btnHandler+'').prop('disabled', false)
                    setTimeout(() => {
                        redirectWindow("");
                    }, 3000);
                }
                else
                {
                    notify("warning", "An Error Occurred", data.response.message); 
                    $('#' + btnHandler + '').html("Try Again");
                    $('#' + btnHandler + '').prop('disabled', false);           
                }
            }, 
            error => {
                errorResponse = error.responseJSON.errors;
                console.log(error);
                
                if("password_current" in errorResponse){                        
                    notify("warning", "Error", errorResponse.password_current);
                }
                else if("newPassword" in errorResponse){
                    notify("warning", "Error", errorResponse.newPassword);
                }
                else if("newPassword_confirmation" in errorResponse){
                    notify("warning", "Error", errorResponse.newPassword_confirmation);
                }

                $('#' + btnHandler + '').html("Try Again");
                $('#' + btnHandler + '').prop('disabled', false);               
            });
        });

        // Update User Avatar
        $('#avatar-file').change(function(event){
            $('#updateAvatarForm').trigger('submit');
        });

        // Handles Avatar Upload
        $('#updateAvatarForm').submit(function(event){
            event.preventDefault();
            dataToSend = new FormData($("#updateAvatarForm")[0]);
            formAction = $(this).attr('action');
            formMethod = $(this).attr('method');
            btnHandler = "updateAvatarBtn"; 
            $.ajax({
                url : formAction,
                method : formMethod,
                data : dataToSend,
                dataType : "JSON",
                contentType : false,
                processData : false,
                cache : false,
                traditional : true,
                beforeSend : function(){
                    $('#'+btnHandler+'').html("Processing");
                    $('#'+btnHandler+'').prop('disabled', true);
                },
                success : function(data){
                    console.log(data);
                    if(data.status == 200){
                        notify("success", data.response.message);
                        setTimeout(() => {
                            redirectWindow("");                        
                        }, 3000);
                    }
                    else
                    {
                        notify("error", "Error", data.response.message);  
                        $('#'+btnHandler+'').html("Try Again");
                        $('#'+btnHandler+'').prop('disabled', false); 
                    }
                },
                error : function(error){
                    errorResponse = error.responseJSON.errors;
                    console.log(error);
                    
                    if("avatar_img" in errorResponse){
                        notify("warning", "Error", errorResponse.avatar_img);
                    }
                    
                    $('#'+btnHandler+'').html("Try Again");
                    $('#'+btnHandler+'').prop('disabled', false);
                }
            });
        })
    </script>
@endsection