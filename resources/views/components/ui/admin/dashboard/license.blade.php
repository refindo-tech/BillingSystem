@props([
    'licenses' => [
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
    ],
])

<div
    class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 xl:mb-0">
    <div class="flex items-center border-b pb-4 justify-between mb-4">
        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">Informasi Lisensi
            <button data-popover-target="popover-description" data-popover-placement="bottom-end" type="button"><svg
                    class="w-4 h-4 ml-2 text-gray-400 hover:text-gray-500" aria-hidden="true" fill="currentColor"
                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg><span class="sr-only">Show information</span>
            </button>
        </h3>
        <div data-popover id="popover-description" role="tooltip"
            class="absolute z-10 invisible inline-block text-sm font-light text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
            <div class="p-3 space-y-2">
                <h3 class="font-semibold text-gray-900 dark:text-white">RLCLOUD ULTIMATE</h3>
                <p>
                    Lisensi layanan yang digunakan oleh aplikasi ini adalah lisensi berbayar yang dikeluarkan oleh
                    <strong>PT. </strong>.
                </p>
                <a href="#"
                    class="flex items-center font-medium text-primary-600 dark:text-primary-500 dark:hover:text-primary-600 hover:text-primary-700">Read
                    more <svg class="w-4 h-4 ml-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg></a>
            </div>
            <div data-popper-arrow></div>
        </div>
    </div>
    <ol class="relative border-gray-200 dark:border-gray-700">
        @foreach ($licenses as $item)
            <li>
                <div class="mb-1 text-base font-medium dark:text-white">{{ $item->title }}
                    {{ $item->count }}/{{ $item->total }}</div>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-4 dark:bg-gray-700">
                    <div class="bg-{{ $item->color }} h-3 rounded-full dark:bg-blue-500"
                        style="width: {{ ($item->count / $item->total) * 100 }}%"></div>
                </div>
            </li>
        @endforeach

    </ol>



    <div
        class="w-full p-4 bg-blue-600 border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="text-xl font-medium text-white dark:text-gray-400">NETPLUS CONNECTION</h5>
        <ul role="list" class="space-y-5 my-2">
            <li class="flex">
                <span class="text-base font-normal leading-tight text-white dark:text-gray-400 ms-3">
                  Instance ID: 1684122076
                  </span>
            </li>
            <li class="flex">
              <span class="text-base font-normal leading-tight text-white dark:text-gray-400 ms-3">
                Expired: 15/12/2024 07:00 WIB
                </span>
          </li>
          <li class="flex">
            <span class="text-base font-normal leading-tight text-white dark:text-gray-400 ms-3">
              Sisa Masa Aktif : 31 HARI
              </span>
        </li>
            
        </ul>
    </div>


</div>
