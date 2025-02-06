<div 
{{-- class="w-full md:max-w-sm p-6 bg-white border border-gray-200 rounded-xl shadow dark:bg-gray-800 dark:border-gray-700" --}}
{{ $attributes->merge(['class' => 'w-full md:max-w-sm p-6 bg-white border border-gray-200 rounded-xl shadow dark:bg-gray-800 dark:border-gray-700'])}}
>
    <div class="flex justify-center items-center mb-4 w-full h-20 rounded-full bg-primary-100 lg:h-20 lg:w-full dark:bg-primary-900">
        {{-- Icon Slot --}}
        {{-- {{ $icon }} --}}
        <i {{ $icon->attributes->merge(['class' => 'fa-solid text-[5rem] bg-clip-text text-transparent bg-gradient-to-r from-net-blue-100 to-net-blue-100']) }}></i>
    </div>
    <h3 class="mb-2 text-2xl font-bold dark:text-white text-center">
        {{-- Title --}}
        {{ $title }}
    </h3>
    <p class="text-gray-500 dark:text-gray-400">
        {{-- Description --}}
        {{ $description }}
    </p>
</div>




