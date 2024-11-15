@props([
  'income' => 24000000,
  'outcome' =>13000000,
])

<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Bulan Ini</h3>
        <a href="#"
            class="inline-flex items-center p-2 text-sm font-medium rounded-lg text-primary-700 hover:bg-gray-100 dark:text-primary-500 dark:hover:bg-gray-700">
            View all
        </a>
    </div>

    <div class="w-full">
      <h3 class="text-base font-normal pb-1 text-green-500 dark:text-gray-400">
        Total Pemasukkan Bulan Ini
      </h3>
      <span class="text-2xl font-bold leading-none text-green-900 sm:text-3xl dark:text-white mt-2">
        Rp. {{ number_format($income, 0, ',', '.') }}
      </span>
    </div>

    <hr class="my-4">

    <div class="w-full">
      <h3 class="text-base font-normal pb-1 text-red-400 dark:text-gray-400">
        Total Pengeluaran Bulan Ini
      </h3>
      <span class="text-2xl font-bold leading-none text-red-900 sm:text-3xl dark:text-white mt-2">
        Rp. {{ number_format($outcome, 0, ',', '.') }}
      </span>
    </div>

    <hr class="my-4">

    <div class="w-full">
      <h3 class="text-base font-normal pb-1 text-blue-500 dark:text-gray-400">
        Total Pendapatan Bulan Ini
      </h3>
      <span class="text-2xl font-bold leading-none text-blue-900 sm:text-3xl dark:text-white mt-2">
        Rp. {{ number_format($income-$outcome, 0, ',', '.') }}
      </span>
    </div>

</div>
