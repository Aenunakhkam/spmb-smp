<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $appName = \App\Models\Setting::where('key', 'app_name')->first()?->value ?? 'SPMB Digital';
            $logoPath = \App\Models\Setting::where('key', 'school_logo_path')->first()?->value;
        @endphp
        <title inertia>{{ $appName }}</title>
        <script>window.appName = "{!! addslashes($appName) !!}";</script>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ $logoPath ? asset('storage/'.$logoPath) : asset('images/logo.png') }}">

        <!-- PWA -->
        <meta name="theme-color" content="#1B5E20">
        <link rel="manifest" href="/build/manifest.webmanifest">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
