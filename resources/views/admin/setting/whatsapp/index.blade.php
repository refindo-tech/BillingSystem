<x-admin-layout title="Whatsapp Messages" active-menu="whatsapp.index" :path="['Whatsapp Contact' => '']">
    <div class="app-container container-xxl">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3 justify-content-center justify-content-md-start">
                    <button id="resend-selected" class="btn btn-primary w-100 w-sm-auto" data-bs-toggle="modal"
                        data-bs-target="#modalResend">
                        <i class="bi bi-chevron-double-right"></i> Kirim Ulang
                    </button>
                    <button id="delete-selected" class="btn btn-danger w-100 w-sm-auto" data-bs-toggle="modal"
                        data-bs-target="#modalDelete">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                    <button id="clear-history" class="btn btn-warning w-100 w-sm-auto">
                        <i class="bi bi-brush"></i> Kosongkan
                    </button>
                    <a href="{{ route('admin:whatsapp.template.index') }}" class="btn btn-info w-100 w-sm-auto">
                        <i class="bi bi-gear"></i> Setting
                    </a>
                    <button id="broadcast-message" class="btn btn-secondary w-100 w-sm-auto" data-bs-toggle="modal"
                        data-bs-target="#modalBlast">
                        <i class="bi bi-people"></i> Blast
                    </button>
                    <button id="send-message" class="btn btn-success w-100 w-sm-auto" data-bs-toggle="modal"
                        data-bs-target="#modalSendMessage">
                        <i class="bi bi-chevron-right"></i> Kirim Pesan
                    </button>
                    <form id="billing-notif-form" action="{{ route('admin:whatsapp.billing') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                            <i class="bi bi-info-circle-fill"></i> Notif Penagihan
                        </button>
                    </form>
                    <form id="billing-notif-isolate" action="{{ route('admin:whatsapp.isolate') }}"
                        method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 w-sm-auto">
                            <i class="bi bi-info-circle-fill"></i> Notif Isolir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="app-container container-xxl">
        <x-datatable :dataTable="$dataTable" />
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Detail Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modalPhone" class="form-label">Nomor Tujuan</label>
                        <input type="text" id="modalPhone" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modalMessageContent" class="form-label">Pesan</label>
                        <textarea id="modalMessageContent" class="form-control" rows="10" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal Kirim Ulang -->
    <div class="modal fade" id="modalResend" tabindex="-1" aria-labelledby="modalResendLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalResendLabel">Kirim Ulang Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin mengirim ulang pesan yang dipilih?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirm-resend-button">Kirim Ulang</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="selected-resend-ids">


    <!-- Modal Kosongkan -->
    <div class="modal fade" id="modalClear" tabindex="-1" aria-labelledby="modalClearLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClearLabel">Kosongkan Riwayat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin mengosongkan riwayat pesan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning">Kosongkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteLabel">Hapus Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pesan yang dipilih?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-batal" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-form" action="{{ route('admin:whatsapp.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ids" id="selected-ids">
                        <button type="submit" id="confirm-delete-button" class="btn btn-danger">Hapus
                            Terpilih</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Blast -->
    <form action="{{ route('admin:whatsapp.blast') }}" method="POST">
        @csrf
        <div class="modal fade" id="modalBlast" tabindex="-1" aria-labelledby="modalBlastLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBlastLabel">Whatsapp Blast</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="tujuan"><strong>Tujuan</strong></label>
                        <select name="tujuan" id="tujuan" class="form-control mt-2">
                            <option value="semua">Semua Pelanggan</option>
                            <option value="aktif">Pelanggan Aktif</option>
                            {{-- <option value="mitra">Semua Mitra</option> --}}
                            <option value="server">Server Tertentu</option>
                            <option value="router">Router Tertentu</option>
                            <option value="nomor">Nomor Tertentu</option>
                        </select>

                        <!-- Select untuk Server -->
                        <div id="server-select" class="mt-2" style="display: none;">
                            <label for="server"><strong>Server</strong></label>
                            <select name="server" id="server" class="form-control">
                                @if (!empty($server) && $server->count() > 0)
                                    @foreach ($server as $srv)
                                        <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                                    @endforeach
                                @else
                                    <option value="">Tidak ada server tersedia</option>
                                @endif
                            </select>
                            @error('server')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Select untuk Router -->
                        <div id="router-select" class="mt-2" style="display: none;">
                            <label for="router"><strong>Router</strong></label>
                            <select name="router" id="router" class="form-control">
                                @if (!empty($router) && $router->count() > 0)
                                    @foreach ($router as $rtr)
                                        <option value="{{ $rtr->id }}">{{ $rtr->name }}</option>
                                    @endforeach
                                @else
                                    <option value="">Tidak ada router tersedia</option>
                                @endif
                            </select>
                            @error('router')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Input Nomor -->
                        <label for="pesan" class="mt-3"><strong>Nomor Tujuan</strong></label>
                        <input type="text" name="phone" id="nomor-input" class="form-control mt-2"
                            placeholder="Masukkan Nomor : 6281xxxxxxxx" style="display: none;">

                        <!-- Input Pesan -->
                        <label for="pesan" class="mt-3"><strong>Pesan</strong></label>
                        <textarea class="form-control" id="notifTextarea" name="pesan" rows="9"
                            placeholder="Contoh: Internet murah dan stabil ada disini..."></textarea>
                        @error('pesan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Blast</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <!-- Modal Kirim Pesan -->
    <div class="modal fade" id="modalSendMessage" tabindex="-1" aria-labelledby="modalSendMessageLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSendMessageLabel">Kirim Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin:whatsapp.send') }}" method="POST">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1"><strong>Tujuan</strong></label>
                                <input class="form-control" name="phone"
                                    placeholder="Contoh: 6281xxxxxxxx">{{ old('pesan') }}</input>
                                @error('phone')
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                    <div data-field="pesan" data-validator="notEmpty">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="box-body mt-5">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1"><strong>Isi Pesan</strong></label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" name="pesan" rows="9"
                                    placeholder="Contoh: Mohon Maaf Kepada Pelanggan Saat Ini Wifi sudah di isolir">{{ old('pesan') }}</textarea>
                                @error('pesan')
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                    <div data-field="pesan" data-validator="notEmpty">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Kirim</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Notif Penagihan -->
    <div class="modal fade" id="modalBillingNotif" tabindex="-1" aria-labelledby="modalBillingNotifLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBillingNotifLabel">Notifikasi Penagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin mengirim notifikasi penagihan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-dark">Kirim Notifikasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Notif Isolir -->
    <div class="modal fade" id="modalIsolateNotif" tabindex="-1" aria-labelledby="modalIsolateNotifLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalIsolateNotifLabel">Notifikasi Isolir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin mengirim notifikasi isolir?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger">Kirim Notifikasi</button>
                </div>
            </div>
        </div>
    </div>

    @push('addon-script')
        <script>
            $(document).ready(function() {
                let table = $('#dataTableBuilder').DataTable(); // Pastikan ID benar

                // Select/Deselect all checkboxes
                $('#select-all').on('click', function() {
                    $('.message-checkbox').prop('checked', this.checked);
                });

                // Jika ada checkbox yang tidak dicentang, uncheck "Select All"
                $('#dataTableBuilder tbody').on('change', '.message-checkbox', function() {
                    if (!this.checked) {
                        $('#select-all').prop('checked', false);
                    }
                });

                $('#confirm-delete-button').on('click', function(event) {
                    event.preventDefault(); // Mencegah form terkirim langsung

                    let selectedIds = [];

                    // Loop melalui semua checkbox yang dicentang
                    $('.message-checkbox:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    // Jika tidak ada ID yang dipilih, tampilkan SweetAlert dan batal
                    if (selectedIds.length === 0) {
                        // Klik tombol batal untuk menutup modal
                        $('.btn-batal').click();
                        // Tunggu beberapa milidetik agar modal benar-benar tertutup sebelum menampilkan SweetAlert
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Oops...',
                                text: 'Pilih setidaknya satu pesan untuk dihapus!',
                                confirmButtonText: 'Kembali'
                            });
                        }, 300); // Delay 300ms untuk memastikan modal tertutup sebelum alert muncul
                        return;
                    }

                    // Masukkan selectedIds ke dalam input hidden
                    $('#selected-ids').val(JSON.stringify(selectedIds));
                    $('.btn-batal').click();
                    // Tampilkan konfirmasi sebelum menghapus
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Pesan yang dipilih akan dihapus secara permanen!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#delete-form').submit(); // Submit form jika dikonfirmasi
                        }
                    });
                });

                $('#confirm-resend-button').on('click', function(event) {
                    event.preventDefault();

                    let selectedIds = [];

                    // Loop semua checkbox yang dicentang
                    $('.message-checkbox:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    // Jika tidak ada pesan yang dipilih, tampilkan SweetAlert
                    if (selectedIds.length === 0) {
                        $('.btn-cancel').click();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Oops...',
                                text: 'Pilih setidaknya satu pesan untuk dikirim ulang!',
                                confirmButtonText: 'Kembali'
                            });
                        }, 300);
                        return;
                    }

                    // Kirim data ke controller menggunakan AJAX
                    $.ajax({
                        url: '/admin/whatsapp/resend', // Pastikan route ini benar
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: JSON.stringify(selectedIds)
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload(); // Refresh halaman setelah sukses
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan.',
                                confirmButtonText: 'Coba Lagi'
                            });
                        }
                    });
                });

            });
        </script>
        <script>
            $(document).ready(function() {
                $("#clear-history").click(function() {
                    Swal.fire({
                        icon: 'question',
                        title: 'Kosongkan data ?',
                        text: 'Semua riwayat pesan akan dihapus',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, kosongkan',
                        cancelButtonText: 'Batalkan',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "/admin/whatsapp/clear", // Sesuaikan dengan route yang dipakai
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.success === true) {
                                        Swal.fire('Dihapus!', response.message, 'success')
                                            .then(() => location
                                                .reload()
                                            ); // Refresh halaman setelah sukses
                                    } else {
                                        Swal.fire('Gagal!', 'Terjadi kesalahan, coba lagi.',
                                            'error');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.',
                                        'error');
                                    console.error("Error:", error);
                                }
                            });
                        }
                    });
                });
            });
        </script>
        <script>
            document.getElementById("tujuan").addEventListener("change", function() {
                let selectedValue = this.value;

                // Sembunyikan semua input tambahan
                document.getElementById("server-select").style.display = "none";
                document.getElementById("router-select").style.display = "none";
                document.getElementById("nomor-input").style.display = "none";

                // Tampilkan sesuai dengan pilihan
                if (selectedValue === "server") {
                    document.getElementById("server-select").style.display = "block";
                } else if (selectedValue === "router") {
                    document.getElementById("router-select").style.display = "block";
                } else if (selectedValue === "nomor") {
                    document.getElementById("nomor-input").style.display = "block";
                }
            });
        </script>
        <script>
            $(document).ready(function() {
                $("#blastButton").click(function() {
                    var tujuan = $("#tujuan").val();
                    var server = $("#server").val() || null;
                    var router = $("#router").val() || null;
                    var pesan = $("#notifTextarea").val();

                    $.ajax({
                        url: "{{ route('admin:whatsapp.blast') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            tujuan: tujuan,
                            server: server,
                            router: router,
                            pesan: pesan
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status === "success") {
                                alert(response.message);
                                $("#modalBlast").modal("hide");
                                location.reload(); // Refresh halaman setelah sukses
                            }
                        },
                        error: function(xhr) {
                            alert("Terjadi kesalahan: " + xhr.responseJSON.message);
                        }
                    });
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("billing-notif-form").addEventListener("submit", function(event) {
                    event.preventDefault(); // Mencegah form langsung terkirim

                    Swal.fire({
                        title: "Kirim Notifikasi?",
                        text: "Apakah Anda yakin ingin mengirim notifikasi penagihan?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Kirim!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            event.target.submit(); // Kirim form setelah konfirmasi
                        }
                    });
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("billing-notif-isolate").addEventListener("submit", function(event) {
                    event.preventDefault(); // Mencegah form langsung terkirim

                    Swal.fire({
                        title: "Kirim Notifikasi?",
                        text: "Apakah Anda yakin ingin mengirim notifikasi penagihan?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Kirim!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            event.target.submit(); // Kirim form setelah konfirmasi
                        }
                    });
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $("#whatsapp_message tbody").on("click", "tr", function() {
                    let phone = $(this).data("phone"); // Ambil nomor tujuan
                    let message = $(this).data("message"); // Ambil pesan

                    $("#modalPhone").val(phone); // Isi input nomor tujuan
                    $("#modalMessageContent").val(message); // Isi textarea pesan

                    $("#messageModal").modal("show"); // Tampilkan modal
                });
            });
        </script>
    @endpush

</x-admin-layout>
