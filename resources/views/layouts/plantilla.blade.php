<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script>
        window.userId = "{{ auth()->user()->id ?? '' }}"
    </script>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="body">
    <x-layouts.header />
    @include('messages.toasts')
    @yield('content')
    <x-layouts.footer />

</body>
</html>
