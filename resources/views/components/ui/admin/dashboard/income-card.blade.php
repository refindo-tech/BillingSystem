@props(['icon' => 'fas fa-wallet',
        'color' => 'bg-blue-500',
        'title' => 'Pendapatan Voucher',
        'count' => '23400000',
        'previous' => '25400000',
        'since' => 'bulan lalu'])

<div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="w-full">
      <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">
        {{ $title }}
      </h3>
      <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white mt-2">
        Rp. {{ number_format($count, 0, ',', '.') }}
      </span>
      <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400 mt-2">
        <span class="flex items-center mr-1.5 text-sm {{ ($count - $previous) > 0 ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
          <i class="fas {{ ($count - $previous) > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}">
            </i> &nbsp;
          <span>{{ number_format((($count - $previous) / $previous) * 100, 2) }}%
          </span>
        </span>
        Sejak {{ $since }}
      </p>
    </div>
    
    <div class="w-full flex justify-end">
      <i class="{{ $icon }} text-5xl text-gray-500 rounded-lg p-3 dark:text-white"></i>
    </div>
</div>

<script>

</script>