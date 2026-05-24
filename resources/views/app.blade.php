<!DOCTYPE html>
<html class="h-full" lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title data-inertia>{{ config('app.name', 'App') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <script>
            // Apply saved or system color scheme synchronously to avoid FOUC.
            // Key matches VueUse's useColorMode/useDark default storage key.
            (function () {
                try {
                    var stored = localStorage.getItem('vueuse-color-scheme');
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    var dark = stored === 'dark' || ((stored === 'auto' || !stored) && prefersDark);
                    if (dark) document.documentElement.classList.add('dark');
                } catch (e) {}
            })();
        </script>
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
