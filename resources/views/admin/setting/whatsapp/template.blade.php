<x-admin-layout title="Template Whatsapp" active-menu="whatsapp.index" :path="['Whatsapp Contact' => '']">
    <div class="app-container container-xxl">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin:whatsapp.index') }}" class="btn btn-primary">
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
                        action="{{ route('admin:whatsapp.template.store') }}">
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
                        action="{{ route('admin:whatsapp.template.store') }}">
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

                <div class="box box-primary mt-5">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Membuat Tiket Komplain</h3>
                        <p class="alert alert-primary">
                            <strong>Format Variable :<br>#NOTIKET# #STATUS# #NAMAPELANGGAN# #SUBJECT# #KELUHAN#
                                #PRIORITAS#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('admin:whatsapp.template.store') }}">
                        @csrf
                        <div class="box-body">
                            <input type="hidden" name="type" value="opentiket">
                            <div class="form-group">
                                <label for="notifTextarea">Isi Pesan Notifikasi Pembuatan Tiket</label>
                                <textarea class="form-control" id="notifTextarea" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Tiket Anda sedang dalam pengerjaan. Silahkan tunggu.">{{ old('pesan_notifikasi', $templateNewTiket->message ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

                <div class="box box-primary mt-5">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Registrasi Pengguna Baru</h3>
                        <p class="alert alert-primary">
                            <strong>Format Variable :<br>#NAMAPELANGGAN# #ALAMATPASANG# #USERNAME# #PASSWORD# #URL#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('admin:whatsapp.template.store') }}">
                        @csrf
                        <div class="box-body">
                            <input type="hidden" name="type" value="userregis">
                            <div class="form-group">
                                <label for="notifTextarea">Isi Pesan Notifikasi Pembuatan Tiket</label>
                                <textarea class="form-control" id="notifTextarea" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Tiket Anda sedang dalam pengerjaan. Silahkan tunggu.">{{ old('pesan_notifikasi', $templateUserRegis->message ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

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
                        action="{{ route('admin:whatsapp.template.store') }}">
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
                                action="{{ route('admin:whatsapp.template.store') }}">
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

                <div class="box box-primary mt-5">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifikasi Membuat Tiket Komplain Teratasi</h3>
                        <p class="alert alert-success">
                            <strong>Format Variable :<br>#NOTIKET# #STATUS# #NAMAPELANGGAN# #SUBJECT# #KELUHAN#
                                #PRIORITAS#</strong>
                        </p>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" name="type" value="closedtiket"
                                action="{{ route('admin:whatsapp.template.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="notifTextarea">Isi Pesan Notifikasi Tiket Teratasi</label>
                                <textarea class="form-control" id="notifTextarea" name="pesan_notifikasi" rows="9"
                                    placeholder="Contoh: Mohon Maaf Kepada Pelanggan Sudah Jatuh Tempo">{{ old('pesan_notifikasi', $templateClosedTiket->message ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer mt-5">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>
