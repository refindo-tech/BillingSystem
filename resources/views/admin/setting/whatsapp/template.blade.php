<x-admin-layout title="Template Whatsapp" active-menu="setting.whatsapp.index" :path="['Whatsapp Contact' => '']">
    <div class="app-container container-xxl">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin:setting.whatsapp.index') }}" class="btn btn-primary">
                Kembali
            </a>
        </div>

        <p class="alert alert-info">
            <strong>Format Tulisan : Miring : _text_ Tebal : *text* Coret : ~teks~ <br>
                Untuk menambahkan emoji seprti : 😊😁🤩🙏👏💯❗✅❎✳️ silahkan buka WhatsApp Web dan copas emoji dari chat
                anda ke kolom template dibawah ini.</strong>
        </p>
        <div class="row">
            <div class="col-md-6">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Registrasi Layanan Baru</h3>
                        <p class="alert alert-primary">
                            <strong>Format Variable :<br>
                                #NOLAYANAN# #NAMAPELANGGAN# #ALAMATPASANG# #PROFILE# #HARGA# #JENISTAGIHAN# #TGLAKTIF#
                                #TGLISOLIR# #PHONE# #URL#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('admin:setting.whatsapp.template.store') }}">
                        @csrf
                        <input type="hidden" name="type" id="type" value="layanan_baru">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="notifTextarea">Isi Pesan Notifikasi Registrasi Otomatis</label>
                                <textarea class="form-control" id="notifTextarea" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Mohon Maaf Kepada Pelanggan Sudah Jatuh Tempo">{{ old('pesan_notifikasi', $templateLayananBaru->message ?? '') }}
                                </textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

                <div class="box box-primary mt-5">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Pembayaran Invoice</h3>
                        <p class="alert alert-success">
                            <strong>Format Variable :<br>
                                #INVOICE# #NOLAYANAN# #NAMAPELANGGAN# #CHANNEL# #TGLBAYAR# #SUBTOTAL# #DISKON#
                                #KODEUNIK# #PPN# #ADM# #TOTAL# #LAYANANAKTIFSAMPAI#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('admin:setting.whatsapp.template.store') }}">
                        @csrf
                        <div class="box-body">
                            <input type="hidden" name="type" value="invoice">
                            <div class="form-group">
                                <label for="notifTextarea1">Isi Pesan Pesan Pembayaran</label>
                                <textarea class="form-control" id="notifTextarea1" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Terima Kasih Kepada Pelanggan Telah Melakukan Pembayaran">{{ old('pesan_notifikasi', $templateInvoice->message ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

                {{-- <div class="box box-primary mt-5">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Penagihan</h3>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" name="type" value="Penagihan">
                            <div class="form-group">
                                <label for="notifTextarea">Isi Pesan Notifikasi Penagihan</label>
                                <textarea class="form-control" id="notifTextarea" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Mohon Maaf Kepada Pelanggan Sudah Jatuh Tempo"></textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div> --}}


            </div>

            {{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalLabel">Contoh Format Tulisan</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <!-- <label for="exampleFormControlTextarea"></label> -->
                                <textarea class="form-control" id="exampleFormControlTextarea" name="pesan_notifikasi" rows="9" disabled>
                                    $nama : Untuk Mengambil Nama Pelanggan
                                    $jatuh_tempo : Untuk Mengambil Tanggal Jatuh Tempo
                                    $tagihan : Untuk Mengambil Harga 
                                    $no_telp : Untuk Mengambil Nomor Telepon Pelanggan
                                    
                                    Contoh Penggunaan 
                                    
                                    Notifikasi Wifi 
                                    Kepada Pelanggan Yth, Saat Ini Wifi Atas Nama $nama Telah memasuki Jatuh Tempo pada Tanggal $jatuh_tempo Harap Lunasi Tagihan Sebesar $tagihan. Jika Belum melakukan Pembayaran Akan otomatis terisolir dalam 1x24 Jam. Terima Kasih. Pembayaran Dapat Akses di https://paynow.biz.id Masukan Username dan Password Berdasarkan nomor yang menerima Wa ini
                                </textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="notifPasang" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalLabel">Contoh Format Tulisan Notif Pasang</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <!-- <label for="exampleFormControlTextarea"></label> -->
                                <textarea class="form-control" id="exampleFormControlTextarea" name="pesan_notifikasi" rows="9" disabled>
                                    $nama : Untuk Mengambil Nama Pelanggan
                                    $alamat : Untuk Mengambil Alamat Pelanggan
                                    $no_telp : Untuk Mengambil Nomor Telepon Pelanggan
                                    $paket : Untuk Mengambil Paket Pilihan Pelanggan
                                    $tgl_pemasangan : Untuk Mengambil Tanggal Pemasangan Pelanggan
                                </textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalLabel">Contoh Format Tulisan</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <!-- <label for="exampleFormControlTextarea"></label> -->
                                <textarea class="form-control" id="exampleFormControlTextarea" name="pesan_notifikasi" rows="9" disabled>
                                    $nama : Untuk Mengambil Nama Pelanggan
                                    $jatuh_tempo : Untuk Mengambil Tanggal Jatuh Tempo
                                    $tagihan : Untuk Mengambil Harga 
                                    $no_telp : Untuk Mengambil Nomor Telepon Pelanggan
                                    $harinin : Untuk Mengambil Waktu dan Tanggal Hari Ini
                                    
                                    Contoh Penggunaan 
                                    
                                    Notifikasi Wifi 
                                    Terima Kasih Kepada $nama Telah Melakukan Pembayaran Sebesar $tagihan Pada $harinin. Invoice Pembayaran Dapat Diambil di https://paynow.biz.id username $no_hp password $no_hp.
                                    
                                    Mohon untuk tidak membalas Pesan ini
                                </textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div> --}}

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Isolir Otomatis</h3>
                        <p class="alert alert-danger">
                            <strong>Format Variable :<br>
                                #NOLAYANAN# #NAMAPELANGGAN# #INVOICE# #PERIODE# #SUBTOTAL# #DISKON# #KODEUNIK# #PPN#
                                #ADM# #TOTAL# #JATUHTEMPO# #VIATRANSFERBANK# #VIAPAYMENTGATEWAY#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('admin:setting.whatsapp.template.store') }}">
                        @csrf
                        <div class="box-body">
                            <input type="hidden" name="type" value="isolir">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Isi Pesan Isolir Otomatis</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Mohon Maaf Kepada Pelanggan Saat Ini Wifi sudah di isolir">{{ old('pesan_notifikasi', $templateIsolir->message ?? '') }}</textarea>
                            </div>

                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

                <div class="box box-primary mt-5">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Penagihan</h3>
                        <p class="alert alert-success">
                            <strong>Format Variable :<br>
                                #NOLAYANAN# #NAMAPELANGGAN# #INVOICE# #PERIODE# #SUBTOTAL# #DISKON# #KODEUNIK# #PPN#
                                #ADM# #TOTAL# #JATUHTEMPO# #VIATRANSFERBANK# #VIAPAYMENTGATEWAY#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" name="type" value="Penagihan"
                                action="{{ route('admin:setting.whatsapp.template.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="notifTextarea">Isi Pesan Notifikasi Penagihan</label>
                                <textarea class="form-control" id="notifTextarea" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Mohon Maaf Kepada Pelanggan Sudah Jatuh Tempo">{{ old('pesan_notifikasi', $templatePenagihan->message ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel">Contoh Format Tulisan</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <!-- <label for="exampleFormControlTextarea"></label> -->
                            <textarea class="form-control" id="exampleFormControlTextarea" name="pesan_notifikasi" rows="9" disabled>
                                $nama : Untuk Mengambil Nama Pelanggan
                                $jatuh_tempo : Untuk Mengambil Tanggal Jatuh Tempo
                                $tagihan : Untuk Mengambil Harga 
                                $no_telp : Untuk Mengambil Nomor Telepon Pelanggan
                                $sekarang_format : Untuk Mendapatkan Waktu Sekarang
                                
                                Contoh Penggunaan 
                                
                                Notifikasi Wifi 
                                Mohon Maaf Kepada Pelanggan Atas nama $nama, Saat Ini Wifi telah di isolir secara otomatis pada tanggal $tanggal_format. Harap Lunasi pembayaran untuk bisa menikmati Akses internet kembali sejumlah $tagihan. Untuk Pembayaran Dapat di bayar di https://paynow.biz.id
                                </textarea>
                        </div>
                    </div>

                </div>
            </div>

        </div> --}}
    </div>
</x-admin-layout>
