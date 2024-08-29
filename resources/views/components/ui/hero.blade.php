<section class="bg-white dark:bg-gray-900">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
                {{-- Payments tool for software companies --}}
                {{ $title }}
            </h1>
            <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                {{-- From checkout to global sales tax compliance, companies around the world use Flowbite to simplify their payment stack. --}}
                {{ $description }}
            </p>
            <button type="button" class="text-white bg-gradient-to-br from-pink-500 to-indigo-600 focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-pink-800 font-medium rounded-full text-lg px-6 py-3.5 text-center me-2 mb-2 transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg hover:bg-red-500">
                {{-- Choose a plan --}}
                Pilih Paket
            </button>            

        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
            {{-- <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/hero/phone-mockup.png" alt="mockup"> --}}
            {{ $image }}
        </div>                
    </div>
</section>