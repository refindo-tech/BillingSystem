@props(['logs' => [
    (object) [
        'created_at' => now(),
        'ip_address' => '127.0.0.1',
        'user' => (object) ['name' => 'OKY'],
        'description' => 'Berhasil login ke aplikasi'
    ],
    (object) [
        'created_at' => now(),
        'ip_address' => '127.0.0.1',
        'user' => (object) ['name' => 'Diana Putri'],
        'description' => 'Berhasil login ke aplikasi'
    ],
]])

<div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 xl:mb-0">
  <div class="flex items-center justify-between mb-4 border-b pb-2">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Logs Aplikasi</h3>
    <a href="#" class="inline-flex items-center p-2 text-sm font-medium rounded-lg text-primary-700 hover:bg-gray-100 dark:text-primary-500 dark:hover:bg-gray-700">
      View all
    </a>
  </div>
  <ol class="relative border-l border-gray-200 dark:border-gray-700">                  

      @foreach ($logs as $log)
        <li class="mb-8 ml-4">
          <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-800 dark:bg-gray-700"></div>
          <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ $log->created_at->format('F d, Y H:i:s') }}. <span class="text-xs">IP address {{ $log->ip_address }}</span></time>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $log->user->name }}</h3>
          <p class="mb-2 text-base font-normal text-gray-500 dark:text-gray-400">
            {{ $log->description }}
          </p>
        </li>
      @endforeach
    
  </ol>
</div>