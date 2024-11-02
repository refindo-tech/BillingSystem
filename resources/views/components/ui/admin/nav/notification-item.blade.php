<!-- resources/views/components/notification-item.blade.php -->
<a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600">
    <div class="flex-shrink-0 relative">
        <img class="rounded-full w-11 h-11" src="{{ $avatar }}" alt="{{ $name }}'s image">
        <div class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 {{ $iconBg }} border border-white rounded-full dark:border-gray-700">
            {!! $icon !!}
        </div>
    </div>
    <div class="w-full pl-3">
        <div class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400">
            {!! $message !!}
        </div>
        <div class="text-xs font-medium text-primary-700 dark:text-primary-400">{{ $time }}</div>
    </div>
</a>
