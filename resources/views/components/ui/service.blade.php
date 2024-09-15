<section class="bg-gray-100 dark:bg-gray-900" id="services">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="max-w-screen-md mb-8 lg:mb-16 justify-center lg:mx-auto text-center">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Total Service Solutions for your needs, trust it with us!</h2>
            <p class="text-gray-500 sm:text-xl dark:text-gray-400">Jenis pekerjaan yang masih berkaitan dengan Jasa Internet dapat kami layani</p>
        </div>
        <div class="flex flex-wrap gap-8 gap-y-12 justify-center"> 

            <x-ui.serviceCard 
                :title="'IT Solution'" 
                :description="'Solusi berbasis teknologi digital untuk perorangan,pelaku usaha UMKM, Perusahaan atau Intansi. Segala macam Layanan Solusi IT, Seperti pengadaan perangkat komputer atau network, CCTV, Sistem Absensi, Acces Control, Instalasi dan konfigurasi jaringan kantor, dan solusi bisnis berbasis IT lainnya.'"
            >
                <x-slot name="icon" class="fa-screwdriver-wrench">
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Cloud Hosting'" 
                :description="'Perusahan yang berkembang tentu memiliki Website Official, E-Mail Server, Storage Server, atau yang lainnya. Support yang lamban menjadi salah satu kendala bagi perkembangan usaha Anda. Kami melayani Jasa Cloud Hosting terpercaya dan fast respon terhadap problem yang Anda alami.'"
            >
                <x-slot name="icon" class="fa-cloud">
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Colocation & Sewa Server Baremetal'" 
                :description="'Selain menyediakan Web/Mail/Storage Server, kami menyewakan perangkat fisik berupa server 1U yang berada di Data Center terkemuka di Jakarta, dengan kapasitas 100Gbps menjadikan usaha Anda semakin lancar dan bebas hambatan.'"
            >
                <x-slot name="icon" class="fa-server">
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Jasa Pembuatan Aplikasi dan Website'" 
                :description="'Kami siap membantu Anda mengembangkan aplikasi dan website sesuai kebutuhan, mulai dari perancangan, desain, hingga publikasi dan pengelolaan. Dengan solusi digital kami, alur pekerjaan Anda akan menjadi lebih efisien dan mudah!'"
                
                >
                <x-slot name="icon" class="fa-globe">
                </x-slot>
            </x-ui.serviceCard>

            <x-ui.serviceCard 
                :title="'Training Center IT'" 
                :description="'Bangun Karir Professionalmu Di Bidang IT Bersama Kami dengan pengajar-pengajar profesional di bidang nya. Jangan Ragu untuk Meraih Sukses, Tentukan Pilihanmu Sekarang Bersama kami.'"
                
            >
                <x-slot name="icon" class="fa-users-gear">
                </x-slot>
            </x-ui.serviceCard>

        </div>
    </div>
  </section>