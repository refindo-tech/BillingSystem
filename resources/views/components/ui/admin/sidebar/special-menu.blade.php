@props(['title', 'icon', 'link' => '#'])

<a href="{{$link}}" data-tooltip-target="tooltip-{{ strtolower($title) }}" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
    <i class="{{ $icon }} w-8 p-1 h-8 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
           
  </a>
  <div id="tooltip-{{ strtolower($title) }}"  role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
    {{ $title }}
      <div class="tooltip-arrow" data-popper-arrow></div>
</div>