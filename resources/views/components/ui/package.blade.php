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
                <span class="text-2xl text-center font-bold text-net-blue-100 dark:text-white">Paket Internet Home</span>
            </div>
            <div
                class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-4 sm:gap-6 xl:gap-4 md:space-y-0 border-4 border-gray-200 rounded-xl dark:border-gray-700 dark:bg-gray-800 p-4 md:p-6 py-12 pt-16 md:py-16 border-dashed">
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
                <span class="text-2xl text-center font-bold text-orange-600 dark:text-white">Paket Internet Coreporate</span>
            </div>
            <div
                class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-4 sm:gap-6 xl:gap-4 lg:space-y-0 border-4 border-gray-200 rounded-xl dark:border-gray-700 dark:bg-gray-800 p-4 md:p-6 pt-16 md:pt-16 border-dashed">
                <x-ui.packageCard2 title="CORP30" description="Paket Internet Coreporate setabil dengan Wireless support dual band 2.4Ghz dan 5Ghz membuat makin nyaman dalam berselancar. Cocok untuk UMKM atau Sekolah dan mendapatkan layanan prioritas."
                    price="350K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 30 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />
<<<<<<< HEAD
                <x-ui.packageCard title="CORP50" description="Paket Internet Coreporate sangat setabil dengan Wireless support dual band 2.4Ghz dan 5Ghz membuat semakin berselancar semakin ngacir. Cocok untuk Sekolah atau Cafe yang membutuhkan Internet kwalitas prima dan mendapatkan layanan prioritas."
=======
                <x-ui.packageCard2 title="CORP50" description="Paket Internet Coreporate sangat setabil dengan Wireless support dual band 2.4Ghz dan 5Ghz membuat semakin berselancar semakin ngacir. Cocok untuk Sekolah atau Café yang membutuhkan Internet kwalitas prima dan mendapatkan layanan prioritas."
>>>>>>> origin/main
                    price="500K" :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 50 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />
                <x-ui.packageCard2 title="CORP100"
                    description="Paket Internet Coreporate super setabil dengan Wireless support dual band 2.4Ghz dan 5Ghz membuat pertukaran data, download, upload, lebih optimal. Cocok untuk Perusahaan atau Instansi dan mendapatkan layanan prioritas." price="800K"
                    :features="[
                        'Jaringan Fiber Optik',
                        'Unlimited, Tanpa Batas Quota!',
                        'Speed up to 100 Mbps',
                        'Sosmed & Streaming Lancar',
                        'Gratis Konsultasi',
                        'Tarif Flat! Tanpa Kenaikan Sepihak',
                    ]" />

                <x-ui.packageCard2 title="CORP150" description="Paket Internet Coreporate paling setabil dengan Wireless support dual band 2.4Ghz dan 5Ghz 
membuat pertukaran data, download, upload dan game semakin optimal. Cocok di gunakan untuk 
Perusahaan, Instansi, streamer game dan mendapatkan layanan prioritas."
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
