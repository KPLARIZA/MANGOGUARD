<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MangoGuard') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#FFA500">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="MangoGuard">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 240px;
            background: #fff;
            border-right: 1px solid #e3e6f0;
            padding-top: 1.5rem;
            z-index: 1030;
            box-shadow: 2px 0 8px rgba(0,0,0,0.03);
        }
        .sidebar .nav-link {
            color: #4e73df;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 0.35rem;
            margin-bottom: 0.25rem;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #f1f3fa;
            color: #224abe;
        }
        .sidebar .sidebar-heading {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #858796;
            padding: 0 1.5rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }
        .main-content {
            margin-left: 240px;
            padding: 2rem 2rem 0 2rem;
            min-height: 100vh;
        }
        .brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4e73df;
            text-align: center;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .brand i {
            color: #ffa500;
        }
        /* Fallback sizing when Tailwind utility classes are unavailable. */
        nav .shrink-0 svg {
            width: 36px;
            height: 36px;
            max-width: 36px;
            max-height: 36px;
            display: block;
        }
        nav svg:not(.farm-block svg) {
            max-width: 24px;
            max-height: 24px;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                width: 100vw;
                height: auto;
                position: relative;
                border-right: none;
                border-bottom: 1px solid #e3e6f0;
                box-shadow: none;
                padding-top: 0.5rem;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
    @yield('head')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>