<section class="bg-gray-100 dark:bg-gray-900" id="services">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="max-w-screen-md mb-8 lg:mb-16 justify-center lg:mx-auto text-center">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Total Service Solutions for your needs, trust it with us!</h2>
            <p class="text-gray-500 sm:text-xl dark:text-gray-400">Jenis pekerjaan yang masih berkaitan dengan Jasa Internet dapat kami layani</p>
        </div>
        <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">

            <x-ui.serviceCard 
                :title="'Jasa Manage Service & Maintenance Jaringan'" 
                :description="'Mengelola semua pekerjaan jaringan lokal tentu sangat merepotkan, terlebih saat looping atau problem tak terduga menjadi momok yang dihadapi oleh sebagian besar perusahaan. Mari solusikan bersama kami! Anda Terima Beres!'"
            >
                <x-slot name="icon">
                    {{-- <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>
                      </svg>     --}}
                      <i class="fa-solid fa-screwdriver-wrench text-[5rem] bg-clip-text text-transparent bg-gradient-to-br from-pink-500 to-indigo-600"></i>
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Cloud Hosting'" 
                :description="'Perusahan yang berkembang tentu memiliki Website Official, E-Mail Server, Storage Server, atau yang lainnya. Support yang lamban menjadi salah satu kendala bagi perkembangan usaha Anda. Kami melayani Jasa Cloud Hosting terpercaya dan fast respon terhadap problem yang Anda alami.'"
            >
                <x-slot name="icon">
                    <i class="fa-solid fa-cloud text-[5rem] bg-clip-text text-transparent bg-gradient-to-br from-pink-500 to-indigo-600"></i>
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Colocation & Sewa Server Baremetal'" 
                :description="'Selain menyediakan Web/Mail/Storage Server, kami menyewakan perangkat fisik berupa server 1U yang berada di Data Center terkemuka di Jakarta, dengan kapasitas 100Gbps menjadikan usaha Anda semakin lancar dan bebas hambatan.'"
            >
                <x-slot name="icon">
                    <i class="fa-solid fa-server text-[5rem] bg-clip-text text-transparent bg-gradient-to-br from-pink-500 to-indigo-600"></i>
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Jasa Pembuatan Website'" 
                :description="'Jika Anda saat ini sedang membutuhkan jasa pembuatan website, mulai dari perancangan, desain & konsep, hingga publikasi dan pengelolaan website Anda, Kami siap mewujudkannya!'"
            >
                <x-slot name="icon">
                    <i class="fa-solid fa-globe text-[5rem] bg-clip-text text-transparent bg-gradient-to-br from-pink-500 to-indigo-600"></i>
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Jasa Pembuatan Aplikasi'" 
                :description="'Di era teknologi yang semakin berkembang pesat ini akan serba mudah jika alur pekerjaan di-implementasikan dalam bentuk aplikasi. Kami siap develop aplikasi sesuai kebutuhan Anda!'"
            >
                <x-slot name="icon">
                    <i class="fa-solid fa-mobile-screen-button text-[5rem] bg-clip-text text-transparent bg-gradient-to-br from-pink-500 to-indigo-600"></i>
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Jasa Konsultasi'" 
                :description="'Selain yang kami sebutkan diatas, ada kalanya ide/gagasan itu muncul setelah sistem berjalan atau problem yang sedang terjadi. Mari diskusikan bersama kami. Problem Anda, Inspirasi Solusi bagi kami.'"
            >
                <x-slot name="icon">
                    <i class="fa-solid fa-users-gear text-[5rem] bg-clip-text text-transparent bg-gradient-to-br from-pink-500 to-indigo-600"></i>
                </x-slot>
            </x-ui.serviceCard>

        </div>
    </div>
  </section>