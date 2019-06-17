@extends('layouts.auth')

@section('title')
    Login
@endsection

@section('content')
    <h1 class="h2">Login &#x1f44b;</h1>
    <hr>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <input class="form-control" type="email" placeholder="Email Address" name="email" />
        </div>
        <div class="form-group pt-1">
            <input class="form-control" type="password" placeholder="Password" name="password" />
            <div class="text-right pt-1">
                <small><a href="#">Forgot password?</a></small>
            </div>
        </div>
        <button class="btn btn-lg btn-block btn-primary" id="loginBtn" type="submit">Log in</button>
        <div class="pt-1">
            <small>Don't have an account yet? <a href="{{ route('register') }}">Create one</a></small>
        </div>
    </form>
@endsection