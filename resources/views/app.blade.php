<!DOCTYPE html>
<html class="h-full">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title inertia>{{ config('app.name', 'App') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite('resources/js/app.js')
        @inertiaHead

        @if ($gaId = config('services.google_analytics.measurement_id'))
            {{-- Google tag (gtag.js) — gated on GOOGLE_ANALYTICS_ID env. Empty = no snippet emitted. --}}
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', @json($gaId));
            </script>
        @endif
    </head>
    <body class="h-full font-sans leading-none antialiased bg-background text-foreground">
        @routes
        @inertia
    </body>
</html>
