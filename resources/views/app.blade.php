<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') === 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico" sizes="any">

    <script>
        window.__META_PAGE_VIEW_EVENT_ID = window.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`;
        window.__PBM_META_EVENTS = @js(app(\App\Analytics\MetaEventMapper::class)->forMode(config('analytics.mode')));
    </script>

    @if (filled(config('meta.pixel_id')))
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=true;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=true;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', @js(config('meta.pixel_id')));
        </script>
    @endif

    <script>
        (() => {
            const appearance = @js($appearance ?? 'system');
            if (appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @fonts
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
    <x-inertia::head>
        <title>{{ config('app.name', 'PBM Landing Page Boilerplate') }}</title>
    </x-inertia::head>
</head>
<body class="font-sans antialiased">
    <x-inertia::app />
</body>
</html>
