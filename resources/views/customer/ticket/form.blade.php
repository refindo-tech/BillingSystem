@php
    // Set mode: use 'add' (or 'edit' when needed)
    $action = $mode == 'edit' ? route('customer:ticket.update', $ticket) : route('customer:ticket.store');
    $method = $mode == 'edit' ? 'PATCH' : 'POST';
    $activeMenu = $mode == 'edit' ? 'customer.ticket.submit' : 'customer.ticket.submit';
@endphp

<x-customer-layout title="{{ $mode == 'edit' ? 'Edit Ticket' : 'Submit Ticket' }}" :active-menu="$activeMenu" :path="['Ticket List' => route('customer:ticket.index'), $mode == 'edit' ? 'Edit Ticket' : 'Submit Ticket' => '']">
    <div class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card card-flush">

            <div class="card-title">
                <div class="alert-success p-4">
                Silahkan ajukan tiket bantuan anda disini. Tim kami akan segera merespon tiket bantuan anda.
                </div>
            </div>

            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Form-->
                <form class="flex flex-col gap-5 form fv-plugins-bootstrap5 fv-plugins-framework" method="POST" action="{{ $action }}">
                    @method($method)
                    @csrf
                    @if ($mode == 'edit')
                        <input type="hidden" name="id" value="{{ $ticket['id'] }}" />
                    @endif

                    <x-form.group.input 
                        name="subject" 
                        required 
                        :value="old('subject', $ticket['subject'] ?? '')" 
                        label="Subject" 
                        placeholder="Enter ticket subject" 
                    />

                    <x-form.group.input 
                        name="message" 
                        type="textarea" 
                        required 
                        :value="old('message', $ticket['message'] ?? '')" 
                        label="Message" 
                        placeholder="Describe your issue" 
                    />

                    <x-form.group.select 
                        name="priority" 
                        label="Priority" 
                        :options="$priorities" 
                        required 
                        :value="old('priority', $ticket['priority'] ?? '')" 
                    />

                    <div class="py-5 row">
                        <div class="col-md-9 offset-md-3">
                            <div class="d-flex">
                                <button type="reset" onclick="window.history.back()" class="btn btn-light me-3">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ $mode == 'edit' ? 'Update' : 'Submit' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->

        <!--begin::Card-->
        <div class="card card-flush mt-5">

            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Form-->
                @if (@$dataTable)
                    {{ $dataTable->table(['class' => 'table align-middle table-row-dashed table-row-fs-6 gy-5 dataTable'], true) }}
                @endif
                <!--end::Form-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->

    </div>
</x-customer-layout>
