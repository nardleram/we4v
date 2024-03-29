<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'we4v') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Elsie&display=swap&family=Quicksand:wght@300;400;500;600;700&display=swap" nonce="lKj8/6tGf$32_#hG+0hG=gByYwQ0&$">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}" nonce="lKj8/6tGf$32_#hG+0hG=gByYwQ0&$">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer nonce="M65RtWhXKlQpO8&x!_dD#08UjYy+$"></script>
    </head>
    <body class="font-sans antialiased bg-we4vBg">
        @inertia
        <div id="groupModals"></div>
        <div id="projectModals"></div>
        <div id="voteModals"></div>
        <div id="membRequestModals"></div>
        <div id="pendingVoteModals"></div>
        <div id="articleModal"></div>
        <div id="pendingNetworkModals"></div>
        <div id="pendingNetworkModals"></div>
        <div id="pmailModal"></div>
    </body>
</html>
