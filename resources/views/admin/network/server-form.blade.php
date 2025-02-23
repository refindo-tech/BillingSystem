@php
    $action = $mode == 'edit' ? route('admin:network.server.update', $server) : route('admin:network.server.store');
    $method = $mode == 'edit' ? 'PATCH' : 'POST';
    $activeMenu = $mode == 'edit'? 'network.server.edit': 'network.server.create';
@endphp
<x-admin-layout title="{{ ucfirst($mode) }} Router" :active-menu="$activeMenu" :path="['List Server' => route('admin:network.server.create'), ucfirst($mode) . ' Server' => '']">
    <div class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Form-->
                <form class="form fv-plugins-bootstrap5 fv-plugins-framework flex flex-col gap-5" method="POST" action="{{ $action }}">
                    @method($method)
                    @csrf
                    @if ($mode == 'edit')
                    <input type="hidden" name="id" value="{{$server['id']}}" />
                    @endif
                    {{-- <x-form.group.select name="enabled" label="Status" :options="['1' => 'Enabled', '0' => 'Disabled']" required :value="@$server['enabled']??1"/> --}}
                    <x-form.group.input name="name" required :value="@$server['name']" label="Server Name" tooltip="Nama harus sesuai dengan server di mikrotik."/>
                    <x-form.group.select name="router_id" label="Router" :options="$routers" required :value="@$pool['router_id']??@$defaultRouterId"/>

                    <div class="row py-5">
                        <div class="col-md-9 offset-md-3">
                            <div class="d-flex">
                                <button type="reset" onclick="window.history.back()"
                                    class="btn btn-light me-3">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>

                </form>


            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
        @if (@$dataTable)
        <div class="card card-flush mt-5">

            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Form-->
               
                    {{ $dataTable->table(['class' => 'table align-middle table-row-dashed table-row-fs-6 gy-5 dataTable'], true) }}
                

                @push('addon-script')
                
                <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
                {{ $dataTable->scripts() }}
               
                @endpush

                <!--end::Form-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
        @endif

    </div>
</x-admin-layout>
