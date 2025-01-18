@props(['icon' => 'fas fa-wallet',
        'color' => 'bg-blue-500',
        'title' => 'Pendapatan Voucher',
        'count' => '545',
        'previous' => '400',
        'total' => '1000',
        'since' => 'bulan lalu'])

<div class="relative items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800 overflow-hidden">
    <div class="w-full">
      <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">
        {{ $title }}
      </h3>
      <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white mt-2">
        {{ number_format($count, 0, ',', '.') }}
      </span>
    </div>
    
    <div class="w-full flex justify-end">
      <i class="{{ $icon }} text-5xl text-gray-500 rounded-lg p-3 dark:text-white"></i>
    </div>

    <div class="w-full bg-gray-200 h-2.5 dark:bg-gray-700 absolute bottom-0 left-0">
        <div class="bg-blue-600 h-2.5" style="width: {{ ($count / $total) * 100 }}%">
        </div>
    </div>

</div>

<script>

</script>