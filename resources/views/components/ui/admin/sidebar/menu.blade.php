<div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
            <div class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <ul class="pb-20 space-y-2">
                  
                    @foreach ($menuItems as $item)
                        <x-ui.admin.sidebar.item :icon="$item['icon']" :title="$item['title']" :link="$item['link']" :children="$item['children'] ?? []" />
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 justify-center hidden w-full p-4 space-x-4 bg-white lg:flex dark:bg-gray-800" sidebar-bottom-menu>
            @foreach ($specialMenuItems as $item)
                <x-ui.admin.sidebar.special-menu :icon="$item['icon']" :title="$item['title']" :link="$item['link']" />
            @endforeach
          </div>

