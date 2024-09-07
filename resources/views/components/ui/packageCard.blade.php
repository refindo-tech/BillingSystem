<div class="flex flex-col p-2 mx-auto max-w-lg text-center text-white bg-net-blue-100 rounded-lg border border-gray-100 shadow dark:border-gray-600 md:p-6 lg:p-4 xl:p-6 dark:bg-gray-800 dark:text-white">
    <h3 class="mb-4 text-2xl font-semibold">{{ $title }}</h3>
    <p class="font-light text-gray-100 sm:text-lg dark:text-gray-400 flex-1">{{ $description }}</p>
    <div class="flex justify-center items-baseline my-8">
        <span class="mr-2 text-4xl font-extrabold">Rp.{{ $price }}</span>
        <span class="text-gray-100 dark:text-gray-400">/bulan</span>
    </div>
    <!-- List -->
    <ul role="list" class="mb-8 space-y-4 text-left flex-1">
        @foreach ($features as $feature)
            <li class="flex items-center space-x-1">
                <!-- Icon -->
                <svg class="flex-shrink-0 w-5 h-5 text-green-200 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                <span>{{ $feature }}</span>
            </li>
        @endforeach
    </ul>
   <a href="https://api.whatsapp.com/send?phone=6287773777005&amp;text=Halo%20Kak,%20Perkenalkan%0ANama%20Saya%20=%20%0AAlamat%20=%20%0AKeperluan%20=%20Saya%20ingin%20berlangganan%20paket%20{{ $title }}%20seharga%20Rp.{{ $price }}%20per%20bulan." 
   class="text-white bg-gradient-to-br from-net-red-50 to-net-red-50 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white dark:focus:ring-primary-900"
   >
    Pesan Sekarang
    </a>
    
</div>
