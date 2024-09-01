<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    {{-- <title>{{ $title ?? 'My App' }} |  NETPLUS Connection</title> --}}
    <title>NETPLUS Connection | {{ $title ?? 'My App' }}</title>
    @vite('resources/css/tailwind.css')
    @vite('resources/css/app.css')
</head>
<body>
    <x-layout.header />

    <main>
        {{ $slot }}
    </main>

    <x-layout.footer />

    @vite('resources/js/app.js')
</body>
</html>
