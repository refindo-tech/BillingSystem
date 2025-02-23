<x-admin-layout title="Manage Router" active-menu="network.router" :path="['List Router' => '']">
    <div class="app-container container-xxl">
        {{-- <x-datatable :dataTable="$dataTable" action-label="Add New Router" :action-url="route('admin:network.router.create')"/> --}}
        <x-datatable :dataTable="$dataTable" :actionUrls="[
        route('admin:network.server.create') => 'Add Server',
        route('admin:network.router.create') => 'Add Router'
]"/>

    </div>
</x-admin-layout>
