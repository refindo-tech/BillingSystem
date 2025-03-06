@php
@endphp
<x-admin-layout title="Whatsapp Gateway" active-menu="setting.whatsapp-gateway" :path="['Gateway' => '']">
    <div class="app-container container-xxl">
        <!--begin::Card-->

        <div class="card card-flush mt-5">

            <div class="card-header bg-primary">
                <h2 class="card-title text-white">
                    Whatsapp Gateway Settings
                </h2>

                <div class="card-toolbar">
                    <form action="{{ route('admin:setting.whatsapp.status') }}" method="GET">
                        @if ($keyWhatsapp->status ?? 'Tidak diketahui')
                            <button type="submit" class="btn btn-sm btn-success">
                                Gateway Aktif
                            </button>
                        @else
                            <button type="submit" class="btn btn-sm btn-info">
                                Aktifkan Gateway
                            </button>
                        @endif
                    </form>

                </div>

            </div>

            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Form-->
                <form class="form fv-plugins-bootstrap5 fv-plugins-framework flex flex-col gap-5" method="POST"
                    action="{{ route('admin:setting.whatsapp-gateway.store') }}">
                    @method('PUT')
                    @csrf
                    <x-form.group.input name="phone" required label="Phone Number" placeholder="62851xxxxxx"
                        value="{{ old('phone', $keyWhatsapp?->phone ?? '') }}">
                        <x-slot:description>
                            <a href="https://fonnte.com/">https://fonnte.com/</a>
                            @error('phone')
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                <div data-field="phone" data-validator="notEmpty">
                                    {{ $message }}
                                </div>
                            @enderror
                        </x-slot:description>
                    </x-form.group.input>

                    <x-form.group.input name="fonnte_key_device" required label="Token Device"
                        placeholder="your_fonnte_key_account"
                        value="{{ old('fonnte_api_key', $keyWhatsapp?->key_device ?? '') }}">
                        <x-slot:description>
                            <a href="https://fonnte.com/">https://fonnte.com/</a>
                            @error('fonnte_api_key')
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                <div data-field="fonnte_api_key" data-validator="notEmpty">
                                    {{ $message }}
                                </div>
                            @enderror
                        </x-slot:description>
                    </x-form.group.input>

                    <x-form.group.input name="fonnte_key_account" required label="Token Account"
                        placeholder="your_fonnte_key_account"
                        value="{{ old('fonnte_api_key', $keyWhatsapp?->key_account ?? '') }}">
                        <x-slot:description>
                            <a href="https://fonnte.com/">https://fonnte.com/</a>
                            @error('fonnte_api_key')
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                <div data-field="fonnte_api_key" data-validator="notEmpty">
                                    {{ $message }}
                                </div>
                            @enderror
                        </x-slot:description>
                    </x-form.group.input>


                    <div class="row py-5">
                        <div class="col-md-9 offset-md-3">
                            <div class="d-flex">
                                <button type="reset" onclick="window.history.back()" class="btn btn-light me-3">
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
    </div>

</x-admin-layout>
