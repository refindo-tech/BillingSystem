<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'My App' }}</title>
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
