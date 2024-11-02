<?php

$menuItems = [
    [
        'icon' => 'fa-solid fa-home',
        'title' => 'Dashboard',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-server',
        'title' => 'Router',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-users',
        'title' => 'Mitra',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-wifi',
        'title' => 'Voucher',
        'link' => '#',
        'children' => [
            ['title' => 'Profile Voucher', 'link' => '#'],
            ['title' => 'Stok Voucher', 'link' => '#'],
            ['title' => 'Voucher Terjual', 'link' => '#'],
            ['title' => 'Voucher Online', 'link' => '#'],
            ['title' => 'Rekap Voucher', 'link' => '#'],
            ['title' => 'Template Manager', 'link' => '#'],
        ],
    ],
    [
        'icon' => 'fa-solid fa-box',
        'title' => 'Langganan',
        'link' => '#',
        'children' => [
            ['title' => 'Profil Langganan', 'link' => '#'],
            ['title' => 'Data Berlangganan', 'link' => '#'],
            ['title' => 'Stop Berlangganan', 'link' => '#'],
            ['title' => 'Langganan Online', 'link' => '#'],
        ],
    ],
    [
        'icon' => 'fa-solid fa-map',
        'title' => 'Map',
        'link' => '#',
        'children' => [
            ['title' => 'Map Pelanggan', 'link' => '#'],
            ['title' => 'Map ODP', 'link' => '#'],
        ],
    ],
    [
        'icon' => 'fa-solid fa-cog',
        'title' => 'OLT',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-cog',
        'title' => 'ODP',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-ticket',
        'title' => 'Tiket',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-credit-card',
        'title' => 'Billing',
        'link' => '#',
        'children' => [
            ['title' => 'Detail Billing', 'link' => '#'],
            ['title' => 'Payment History', 'link' => '#'],
            ['title' => 'Invoices', 'link' => '#'],
        ],
    ],

    [
        'icon' => 'fa-solid fa-shuffle',
        'title' => 'Transaksi',
        'link' => '#',
    ],

    [
        'icon' => 'fa-regular fa-credit-card',
        'title' => 'Pembayaran',
        'link' => '#',
    ],

    [
        'icon' => 'fa-brands fa-whatsapp',
        'title' => 'Whatsapp',
        'link' => '#',
    ],

    // [
    //     'icon' => 'fa-solid fa-cog',
    //     'title' => 'Settings',
    //     'link' => '#',
    // ],
    // [
    //     'icon' => 'fa-solid fa-user-tie',
    //     'title' => 'Admin',
    //     'link' => '#',
    // ],
    // [
    //     'icon' => 'fa-solid fa-circle-info',
    //     'title' => 'Logs',
    //     'link' => '#',
    // ],
];

$specialMenuItems = [
    [
        'icon' => 'fa-solid fa-cog',
        'title' => 'Settings Page',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-user-tie',
        'title' => 'Admin',
        'link' => '#',
    ],
    [
        'icon' => 'fa-solid fa-circle-info',
        'title' => 'Logs',
        'link' => '#',
    ],
];

?>

<aside id="sidebar" class="fixed top-0 left-0 z-20 flex-col flex-shrink-0 hidden w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width" aria-label="Sidebar">
    <x-ui.admin.sidebar.menu :menuItems="$menuItems" :specialMenuItems="$specialMenuItems" />
</aside>
  
<div class="fixed inset-0 z-10 hidden bg-gray-900/50 dark:bg-gray-900/90" id="sidebarBackdrop"></div>