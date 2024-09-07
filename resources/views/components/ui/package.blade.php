<section class="bg-white dark:bg-gray-900" id="package">
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12 lg:items-start pt-16 lg:pt-20">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white lg:items-start pt-20 lg:pt-20">
                {{ $title }}
            </h2>
            <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">
                {{ $description }}
            </p>
        </div>
        <div class="relative pt-12">
            <div
                class="flex justify-center items-center mb-4 border-4 border-gray-200 rounded-xl dark:border-gray-700 dark:bg-gray-800 p-4 md:p-6 border-dashed w-56 md:w-96 mx-auto bg-white absolute -top-4 left-1/2 transform -translate-x-1/2">
                <span class="text-2xl text-center font-bold text-gray-900 dark:text-white">Paket Internet Home</span>
            </div>
            <div
                class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-4 sm:gap-6 xl:gap-4 lg:space-y-0 border-4 border-gray-200 rounded-xl dark:border-gray-700 dark:bg-gray-800 p-4 md:p-6 py-12 pt-20 border-dashed">
                <x-ui.packageCard title="Subsidi" description="Paket internet murah, stabil dan unlimited."
                    price="174K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 15 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />
                <x-ui.packageCard title="Komersil" description="Cocok untuk penggunaan skala kecil dan menengah."
                    price="225K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 35 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />
                <x-ui.packageCard title="Premium"
                    description="Layanan premium dengan kecepatan tinggi pada skala besar." price="325K"
                    :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 60 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />

                <x-ui.packageCard title="Sultan" description="Untuk para Sultan yang membutuhkan layanan terbaik."
                    price="450K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 100 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />

            </div>
        </div>

        <div class="relative pt-12">
            <div
                class="flex justify-center items-center mb-4 border-4 border-gray-200 rounded-xl dark:border-gray-700 dark:bg-gray-800 p-4 md:p-6  border-dashed w-56 md:w-96 mx-auto bg-white absolute -top-[1.25rem] left-1/2 transform -translate-x-1/2">
                <span class="text-2xl text-center font-bold text-gray-900 dark:text-white">Paket Internet Coreporate</span>
            </div>
            <div
                class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-4 sm:gap-6 xl:gap-4 lg:space-y-0 border-4 border-gray-200 rounded-xl dark:border-gray-700 dark:bg-gray-800 p-4 pt-20 md:p-6 pt-12 border-dashed">
                <x-ui.packageCard title="CORP30" description="Paket internet murah, stabil dan unlimited."
                    price="350K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 30 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />
                <x-ui.packageCard title="CORP50" description="Cocok untuk penggunaan skala kecil dan menengah."
                    price="500K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 50 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />
                <x-ui.packageCard title="CORP100"
                    description="Layanan premium dengan kecepatan tinggi pada skala besar." price="800K"
                    :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 100 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />

                <x-ui.packageCard title="CORP150" description="Untuk para Sultan yang membutuhkan layanan terbaik."
                    price="1.2JT" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 150 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />

            </div>
        </div>

    </div>
</section>
