<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'we4v') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@200;300;400;500;600;700&display=swap" nonce="lKj8/6tGf$32_#hG+0hG=gByYwQ0&%">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}" nonce="lKj8/6tGf$32_#hG+0hG=gByYwQ0&%">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer nonce="M65RtWhXKlQpO8&x!_dD#08UjYy+%"></script>
    </head>
    <body class="font-sans antialiased">
        @yield('content')
    </body>
</html>
