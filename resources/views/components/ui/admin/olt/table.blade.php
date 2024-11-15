@props([
    'olts' => [
        (object) [
            'id' => 1,
            'name' => 'Olt 1',
            'vendor' => 'Vendor 1',
            'model' => 'Model 1',
            'jenis' => 'Jenis 1',
            'ip_remote' => '192.168.1.1',
            'port' => '8080',
            'community_read' => 'public',
            'community_write' => 'private',
            'script' => 'Script 1',
        ],
        (object) [
            'id' => 2,
            'name' => 'Olt 2',
            'vendor' => 'Vendor 2',
            'model' => 'Model 2',
            'jenis' => 'Jenis 2',
            'ip_remote' => '192.168.1.2',
            'port' => '8081',
            'community_read' => 'public',
            'community_write' => 'private',
            'script' => 'Script 2',
        ],
    ]
])

<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-all" aria-describedby="checkbox-1" type="checkbox"
                                        class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                ID
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                NAMA
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                VENDOR
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                MODEL
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                JENIS
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                IP REMOTE
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                PORT
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                COMMUNITY READ
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                COMMUNITY WRITE
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                SCRIPT
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                ACTION
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($olts as $olt)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-{{ $olt->id }}" aria-describedby="checkbox-1"
                                        type="checkbox"
                                        class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-{{ $olt->id }}" class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->id }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->name }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->vendor }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->model }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->jenis }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->ip_remote }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->port }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->community_read }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->community_write }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $olt->script }}
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap justify-end items-end flex">
                                <button type="button" id="updateOltButton"
                                    data-drawer-target="drawer-update-olt-default"
                                    data-drawer-show="drawer-update-olt-default"
                                    aria-controls="drawer-update-olt-default"
                                    data-drawer-placement="right"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-primary-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                        </path>
                                        <path fill-rule="evenodd"
                                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Update
                                </button>
                                <button type="button" id="deleteOltButton"
                                    data-drawer-target="drawer-delete-olt-default"
                                    data-drawer-show="drawer-delete-olt-default"
                                    aria-controls="drawer-delete-olt-default"
                                    data-drawer-placement="right"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Delete item
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div
class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
<div class="flex items-center mb-4 sm:mb-0">
    <a href="#"
        class="inline-flex justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"></path>
        </svg>
    </a>
    <a href="#"
        class="inline-flex justify-center p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
        </svg>
    </a>
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">Showing <span
            class="font-semibold text-gray-900 dark:text-white">1-20</span> of <span
            class="font-semibold text-gray-900 dark:text-white">2290</span></span>
</div>
<div class="flex items-center space-x-3">
    <a href="#"
        class="inline-flex items-center justify-center flex-1 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-primary-800">
        <svg class="w-5 h-5 mr-1 -ml-1"" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"></path>
        </svg>
        Previous
    </a>
    <a href="#"
        class="inline-flex items-center justify-center flex-1 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-primary-800">
        Next
        <svg class="w-5 h-5 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
        </svg>
    </a>
</div>
</div>
<x-ui.admin.olt.form-delete />
<x-ui.admin.olt.form-update />