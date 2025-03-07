<x-admin-layout title="Prepaid Users" active-menu="prepaid.user" :path="['List Prepaid User' => '']">
    <div class="app-container container-xxl">
        <div class="row mb-3">
            <div class="col-md-2">
                <select id="filter-status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="on">AKTIF</option>
                    <option value="off">TIDAK AKTIF</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-plan-name" class="form-control">
                    <option value="">Semua Paket</option>
                    @foreach (\App\Models\Plan::pluck('name') as $plan)
                        <option value="{{ $plan }}">{{ $plan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-plan-type" class="form-control">
                    <option value="">Semua Tipe</option>
                    @foreach (\App\Models\Plan::pluck('type')->unique() as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-validity-cycle" class="form-control">
                    <option value="">Semua Siklus</option>
                    @foreach (\App\Enum\ValidityCycle::cases() as $cycle)
                        <option value="{{ $cycle->value }}">{{ $cycle->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-router-name" class="form-control">
                    <option value="">Semua Router</option>
                    @foreach (\App\Models\Router::pluck('name') as $router)
                        <option value="{{ $router }}">{{ $router }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-server-name" class="form-control">
                    <option value="">Semua Server</option>
                    @foreach (\App\Models\Server::pluck('name') as $server)
                        <option value="{{ $server }}">{{ $server }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <x-datatable :dataTable="$dataTable" action-label="Recharge Account" :action-url="route('admin:prepaid.user.create')" />
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('.dataTable').DataTable();

                // Event listener untuk dropdown filter
                $('#filter-status, #filter-plan-name, #filter-plan-type, #filter-validity-cycle, #filter-router-name, #filter-server-name')
                    .on('change', function() {
                        table.draw();
                    });

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var status = $('#filter-status').val();
                    var planName = $('#filter-plan-name').val();
                    var planType = $('#filter-plan-type').val();
                    var validityCycle = $('#filter-validity-cycle').val();
                    var routerName = $('#filter-router-name').val();
                    var serverName = $('#filter-server-name').val();

                    // Ambil nilai dari kolom yang sesuai di tabel
                    var dataStatus = data[5]; // Sesuaikan indeks kolom
                    var dataPlanName = data[6];
                    var dataPlanType = data[7];
                    var dataValidityCycle = data[8];
                    var dataRouterName = data[13];
                    var dataServerName = data[14];

                    if ((status === "" || dataStatus === status) &&
                        (planName === "" || dataPlanName === planName) &&
                        (planType === "" || dataPlanType === planType) &&
                        (validityCycle === "" || dataValidityCycle === validityCycle) &&
                        (routerName === "" || dataRouterName === routerName) &&
                        (serverName === "" || dataServerName === serverName)) {
                        return true;
                    }
                    return false;
                });
            });
        </script>
    @endpush


</x-admin-layout>
