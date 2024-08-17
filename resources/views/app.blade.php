<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['theme-light' => Cookie::get('theme') == 'light', 'theme-dark' => Cookie::get('theme') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

        <title inertia>{{ config('app.name', 'Feeed') }}</title>

		@if (Cookie::get('theme') == 'light')
			<meta name="theme-color" content="hsl(213, 39%, 95%)" />
		@elseif (Cookie::get('theme') == 'dark')
			<meta name="theme-color" content="hsl(215, 71%, 6%)" />
		@else
			<meta name="theme-color" content="hsl(213, 39%, 95%)" />
		@endif

		<meta name="mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<link rel="icon" type="image/svg+xml" href="/logo.svg" />

        <!-- Scripts -->
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
