@extends('layouts.auth')

@section('title')
    Register
@endsection

@section('content')
    <h1 class="h2">Sign Up &#x1f44b;</h1>
    <hr>
    <form method="POST" action=" {{ route('register') }}">
        @csrf
        <div class="form-group">
            <input class="form-control" type="text" placeholder="Full Name" name="fullname" required/>
        </div>
        <div class="form-group pt-1">
            <input class="form-control" type="email" placeholder="Email Address" name="email" required/>
        </div>
        <div class="form-group pt-1">
            <input class="form-control" type="password" placeholder="Password" name="password" required/>
            <div class="text-center text-danger pt-1">
                <small>Your password should be at least 6 characters</small>
            </div>
        </div>
        <button class="btn btn-lg btn-block btn-primary" id="registerBtn" type="submit">Register</button>
        <div class="pt-1">
            <small>Have an account ? <a href="{{ route('login') }}">Login</a></small>
        </div>
    </form>
@endsection