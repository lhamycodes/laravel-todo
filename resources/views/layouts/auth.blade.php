<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <title>Todo App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A To Do App">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Gothic+A1" rel="stylesheet">
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('assets/vendor/toastr/toastr.min.css') }}" rel="stylesheet" />

</head>
<body>

    <div class="main-container fullscreen">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-6 col-md-7">
                    <div class="text-center">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('assets/vendor/jquery-3.2.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/autosize.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/prism.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/toastr/toastr.init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/draggable.bundle.legacy.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/swap-animation.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/dropzone.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/list.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/theme.js') }}"></script>

    <script type="text/javascript">
        $('form').submit(function(event){
            event.preventDefault();
            dataToSend = $(this).serialize();
            formAction = $(this).attr('action');
            formMethod = $(this).attr('method');
            btnHandler = $("button[type=submit]",this).attr('id'); 
            $.ajax({
                url : formAction,
                method : formMethod,
                data : dataToSend,
                dataType : "JSON",
                beforeSend : function(){
                    $('#'+btnHandler+'').html("Processing");
                    $('#'+btnHandler+'').prop('disabled', true);
                },
                success : function(data){
                    console.log(data);
                    if(data.status == 200){
                        notify("success", data.response.message);
                        setTimeout(() => {
                            window.location.href = data.response.redirectTo;                        
                        }, 3000);
                    }
                    else
                    {
                        notify("error", "Login Failed", data.response);  
                        $('#'+btnHandler+'').html("Try Again");
                        $('#'+btnHandler+'').prop('disabled', false); 
                    }
                },
                error : function(error){
                    errorResponse = error.responseJSON.errors;
                    console.log(error);
                    if("email" in errorResponse){
                        notify("warning", "Error !", errorResponse.email[0]);
                    }
                    else if("password" in errorResponse){
                        notify("warning", "Error !", errorResponse.password[0]);
                    }
                    else if("fullname" in errorResponse){
                        notify("warning", "Error !", errorResponse.fullname[0]);
                    }

                    $('#'+btnHandler+'').html("Try Again");
                    $('#'+btnHandler+'').prop('disabled', false);
                }
            });
        })
    </script>
</body>
</html>