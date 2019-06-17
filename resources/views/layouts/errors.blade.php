<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <title>@yield('title') - Todo App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A To Do App">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon" type="image/x-icon">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Gothic+A1" rel="stylesheet">
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" type="text/css" media="all" />

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
    <script type="text/javascript" src="{{ asset('assets/vendor/draggable.bundle.legacy.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/swap-animation.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/dropzone.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/list.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/theme.js') }}"></script>
    <script type="text/javascript">
        function goBack(event){
            event.preventDefault();
            window.history.back();
        }
    </script>
</body>
</html>