@extends('layouts.plantilla')

@section('title', 'Welcome')

@section('content')
    <main class="container-fluid ">
        <h1>Welcome {{ auth()->user()->name ?? '' }}</h1>
        <x-auth.singup.modal />
        <x-auth.singin.modal />
    </main>
@endsection
