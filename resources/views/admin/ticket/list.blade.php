<x-admin-layout title="Manage Ticket" active-menu="ticket.index" :path="['ListTicket' => '']">
    <div class="app-container container-xxl">
        <x-datatable :dataTable="$dataTable" action-label="Add New Ticket" :action-url="route('admin:ticket.create')"/>
    </div>
</x-admin-layout>
