<!DOCTYPE html>
<html class="h-full">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title inertia>{{ config('app.name', 'App') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="h-full font-sans leading-none antialiased bg-background text-foreground">
        @routes
        @inertia
    </body>
</html>
