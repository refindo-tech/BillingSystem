<div id="drawer-create-router-default"
class="fixed top-0 right-0 z-50 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">
<h5 id="drawer-label"
    class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">New
    Router</h5>
<button type="button" data-drawer-dismiss="drawer-create-router-default"
    aria-controls="drawer-create-router-default"
    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd"></path>
    </svg>
    <span class="sr-only">Close menu</span>
</button>
<form action="#">
    <div class="space-y-4">
        <div>
            <label for="name"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Router</label>
            <input type="text" name="title" id="name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type router name" required="">
        </div>

        <div>
            <label for="connection"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Koneksi</label>
                <select id="connection"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                <option selected="">Pilih Koneksi</option>
                <option value="public">IP Publik</option>
                <option value="radius">VPN Radius</option>
            </select>
        </div>
        <div class="bottom-0 left-0 flex justify-center w-full pb-4 space-x-4 md:px-4 md:absolute">
            <button type="submit"
                class="text-white w-full justify-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-primary-800">
                Add router
            </button>
            <button type="button" data-drawer-dismiss="drawer-create-router-default"
                aria-controls="drawer-create-router-default"
                class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                <svg aria-hidden="true" class="w-5 h-5 -ml-1 sm:mr-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </button>
        </div>
</form>
</div>