@extends('layouts.plantilla')
@section('title', trans('Sign Up'))
@section('content')
    <main class="container-fluid">
        <div class="grid align-items-center" style="--bs-columns: 3; --bs-gap: 1rem;">
            <div class="g-col-3 g-col-lg-1 g-start-lg-2">
                <x-auth.singup.card />
            </div>
        </div>
    </main>
@endsection
