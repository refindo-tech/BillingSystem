
@props(['icon', 'title', 'link' => '#', 'children' => []])

<li>
    @if (empty($children))
        <!-- Standalone item -->
        <a href="{{ $link }}" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700">
            <i class="{{ $icon }} w-6 p-1 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
            <span class="ml-3">{{ $title }}</span>
        </a>
    @else
        <!-- Item with dropdown -->
        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700" aria-controls="dropdown-{{ strtolower($title) }}" data-collapse-toggle="dropdown-{{ strtolower($title) }}">
            <i class="{{ $icon }} w-6 p-1 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"></i>
            <span class="flex-1 ml-3 text-left whitespace-nowrap">{{ $title }}</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
        <ul id="dropdown-{{ strtolower($title) }}" class="hidden py-2 space-y-2">
            @foreach ($children as $child)
                <li>
                    <a href="{{ $child['link'] }}" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                        {{ $child['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>
