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

        <div class="row mb-3 mt-3">
            <div class="col-md-3">
                <select id="batch-action" class="form-control">
                    <option value="">Pilih Aksi Batch</option>
                    <option value="on">Set Status On</option>
                    <option value="off">Set Status Off</option>
                    <option value="change_router">Ganti Router</option>
                    <option value="change_server">Ganti Server</option>
                    <option value="print_details">Cetak Detail</option>
                    <option value="print_invoice">Cetak Invoice</option>
                    <option value="delete">Hapus Data</option>
                </select>
            </div>
            <div class="col-md-3 d-none" id="router-selection">
                <select id="new-router" class="form-control">
                    <option value="">Pilih Router</option>
                    @foreach (\App\Models\Router::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-none" id="server-selection">
                <select id="new-server" class="form-control">
                    <option value="">Pilih Server</option>
                    @foreach (\App\Models\Server::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <button id="apply-batch-action" class="btn btn-primary">Terapkan</button>
            </div>
        </div>

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

        var dataStatus = data[5];
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

    // Select All Checkbox
    $('#select-all').on('click', function() {
        $('.row-checkbox').prop('checked', this.checked);
    });

    // Tampilkan dropdown pemilihan router jika pilih "Ganti Router"
    $('#batch-action').on('change', function() {
        if ($(this).val() === 'change_router') {
            $('#router-selection').removeClass('d-none');
        } else {
            $('#router-selection').addClass('d-none');
        }
    });

    // Tampilkan dropdown pemilihan server
    $('#batch-action').on('change', function() {
        if ($(this).val() === 'change_server') {
            $('#server-selection').removeClass('d-none');
        } else {
            $('#server-selection').addClass('d-none');
        }
    });

    // Handle tombol submit batch processing
    $('#apply-batch-action').on('click', function() {
        var selectedAction = $('#batch-action').val();
        var selectedIds = $('.row-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Peringatan', 'Pilih setidaknya satu baris untuk melakukan aksi.', 'warning');
            return;
        }

        if (selectedAction === 'delete') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    processBatchAction(selectedAction, selectedIds);
                }
            });
        } else {
            processBatchAction(selectedAction, selectedIds);
        }
    });

    function processBatchAction(action, ids) {
        var postData = {
            _token: '{{ csrf_token() }}',
            ids: ids,
            action: action,
        };

        if (action === 'change_router') {
            var newRouter = $('#new-router').val();
            if (!newRouter) {
                Swal.fire('Peringatan', 'Pilih router yang baru.', 'warning');
                return;
            }
            postData.router_id = newRouter;
        }

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your request.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.post("{{ route('admin:prepaid.user.batch-action') }}", postData, function(response) {
            Swal.fire('Sukses', response.message, 'success');
            table.ajax.reload();
        }).fail(function(response) {
            Swal.fire('Error', response.responseJSON.message, 'error');
        });
    }
});

        </script>
    @endpush


</x-admin-layout>
