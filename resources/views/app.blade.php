<!DOCTYPE html>
<html class="h-full bg-gray-100">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title inertia>{{ config('app.name', 'App') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="font-sans leading-none text-gray-700 antialiased">
        @inertia
    </body>
</html>
