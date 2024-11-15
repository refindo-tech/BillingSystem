@php
    $title = 'Admin Dashboard';
    $logs = [
        (object) [
            'created_at' => now(),
            'ip_address' => '127.0.0.1',
            'user' => (object) ['name' => 'OKY'],
            'description' => 'Berhasil login ke aplikasi',
        ],
        (object) [
            'created_at' => now(),
            'ip_address' => '127.0.0.1',
            'user' => (object) ['name' => 'Diana A'],
            'description' => 'Berhasil login ke aplikasi',
        ],
    ];

    $license = [
        (object) [
            'title' => 'Total Sesi Online',
            'count' => 560,
            'total' => 720,
            'color' => 'blue-600',
        ],
        (object) [
            'title' => 'Total Voucher',
            'count' => 60,
            'total' => 50000,
            'color' => 'blue-600',
        ],
        (object) [
            'title' => 'Total Berlangganan',
            'count' => 670,
            'total' => 700,
            'color' => 'blue-600',
        ],
        (object) [
            'title' => 'Total Router',
            'count' => 2,
            'total' => 15,
            'color' => 'blue-600',
        ],
    ];

@endphp

<x-layout.admin.app title="{{ $title }}">
    <div class="grid gap-4 xl:grid-cols-2">
        <x-ui.admin.dashboard.income-card title="Pendapatan Voucher" count="23400000" previous="25400000" since="kemarin"
            icon="fas fa-wifi" />
        <x-ui.admin.dashboard.income-card title="Pendapatan Invoice" count="24452000" previous="15400000" since="kemarin"
            icon="fas fa-file-invoice" />
    </div>

    <div class="grid gap-4 mt-4 md:grid-cols-3">
        <x-ui.admin.dashboard.progress-card title="Voucher Online" count="200" previous="500" since="kemarin"
            icon="fas fa-wifi" />
        <x-ui.admin.dashboard.progress-card title="Langganan Online" count="521" previous="511" since="kemarin"
            icon="fas fa-users" />
        <x-ui.admin.dashboard.progress-card title="Terisolir" count="877" previous="900" since="kemarin"
            icon="fas fa-file-invoice" />
    </div>

    <div class="grid gap-4 mt-4 xl:grid-cols-2 2xl:grid-cols-3">
        <x-ui.admin.dashboard.logs :logs="$logs" />
        <x-ui.admin.dashboard.license :license="$license" />
    </div>

    <div class="grid gap-4 mt-4 xl:grid-cols-2 2xl:grid-cols-3">
        <x-ui.admin.dashboard.transaction :income=24000000 :outcome=14500000 />
        <x-ui.admin.dashboard.income/>
    </div>

</x-layout.admin.app>
