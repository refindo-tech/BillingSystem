<!-- resources/views/notification.blade.php -->
<button type="button" data-dropdown-toggle="notification-dropdown" class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
  <span class="sr-only">View notifications</span>
  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
</button>

<div class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-white divide-y divide-gray-100 rounded shadow-lg dark:divide-gray-600 dark:bg-gray-700" id="notification-dropdown">
  <div class="block px-4 py-2 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
      Notifications
  </div>
  <div>
      @php
          $notifications = [
              [
                  'avatar' => 'https://api.dicebear.com/9.x/icons/svg?seed=Riley',
                  'name' => 'Bonnie Green',
                  'message' => 'New message from <span class="font-semibold text-gray-900 dark:text-white">Bonnie Green</span>: "Hey, what\'s up? All set for the presentation?"',
                  'time' => 'a few moments ago',
                  'icon' => '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z"></path><path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path></svg>',
                  'iconBg' => 'bg-primary-700',
              ],
              [
                  'avatar' => 'https://api.dicebear.com/9.x/icons/svg?seed=Kai',
                  'name' => 'Kai Watson',
                  'message' => 'New message from <span class="font-semibold text-gray-900 dark:text-white">Kai Watson</span>: "Great, I\'ll see you at the office."',
                  'time' => '2 hours ago',
                  'icon' => '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z"></path><path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path></svg>',
                  'iconBg' => 'bg-primary-700',
              ],
          ];
      @endphp

      @foreach ($notifications as $notification)
          <x-ui.admin.nav.notification-item
              :avatar="$notification['avatar']" 
              :name="$notification['name']" 
              :message="$notification['message']" 
              :time="$notification['time']" 
              :icon="$notification['icon']"
              :iconBg="$notification['iconBg']"
          />
      @endforeach
  </div>
  <a href="#" class="block py-2 text-base font-normal text-center text-gray-900 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:hover:underline">
      <div class="inline-flex items-center ">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
          View all
      </div>
  </a>
</div>
