<div id="drawer-create-biller-default"
class="fixed top-0 right-0 z-50 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">
<h5 id="drawer-label"
    class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">New
    Biller</h5>
<button type="button" data-drawer-dismiss="drawer-create-biller-default"
    aria-controls="drawer-create-biller-default"
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
            <label for="nama_lengkap"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type full name" required="">
        </div>
        <div>
            <label for="no_identitas"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Identitas</label>
            <input type="text" name="no_identitas" id="no_identitas"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type identity number" required="">
        </div>
        <div>
            <label for="phone"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone</label>
            <input type="text" name="phone" id="phone"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type phone number" required="">
        </div>
        <div>
            <label for="alamat"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
            <textarea name="alamat" id="alamat"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type address" required=""></textarea>
        </div>
        <div>
            <label for="username"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
            <input type="text" name="username" id="username"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type username" required="">
        </div>
        <div>
            <label for="password"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
            <input type="password" name="password" id="password"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type password" required="">
        </div>
        <div>
            <label for="tanggal_bergabung"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Bergabung</label>
            <input type="datetime-local" name="tanggal_bergabung" id="tanggal_bergabung"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required="">
        </div>
        <div>
            <label for="limit_hutang"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Limit Hutang</label>
            <input type="number" name="limit_hutang" id="limit_hutang"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type debt limit" required="">
        </div>
        <div>
            <label for="kode_unik"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Unik</label>
            <input type="text" name="kode_unik" id="kode_unik"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type unique code" required="">
        </div>
        <div>
            <label for="komisi"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Komisi</label>
            <input type="number" name="komisi" id="komisi"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Type commission" required="">
        </div>
        <div class="bottom-0 left-0 flex justify-center w-full pb-4 space-x-4 md:px-4">
            <button type="submit"
                class="text-white w-full justify-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-primary-800">
                Add biller
            </button>
            <button type="button" data-drawer-dismiss="drawer-create-biller-default"
                aria-controls="drawer-create-biller-default"
                class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                <svg aria-hidden="true" class="w-5 h-5 -ml-1 sm:mr-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </button>
        </div>
    </div>
</form>
</div>