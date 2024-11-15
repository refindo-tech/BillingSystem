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

    <x-layout.admin.header />

    <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
        <x-layout.admin.sidebar />
        <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900">
        <main>
            <div class="px-4 pt-6">
            {{ $slot }}
            </div>
        </main>
        

        <x-layout.admin.footer />
    </div>

    </div>

    @vite('resources/js/app.js')
</body>

</html>
