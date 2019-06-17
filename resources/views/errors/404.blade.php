@extends('layouts.errors')

@section('title', 'Page Not Found');

@section('content')
    <h1 class="display-1 text-primary">4&#x1f635;4</h1>
    <p>
        The page you were looking for was not found. 
        <br><br>
        <button class="btn btn-primary" onclick="goBack(event)">Go Back</button>
    </p>
@endsection