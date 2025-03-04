@php
@endphp
<x-admin-layout title="Tripay - Payment Gateway" active-menu="setting.tripay" :path="['Tripay' => '']">
    <div class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body" x-data="tripayForm()" x-init="init()">
                <!--begin::Form-->
                <form class="form fv-plugins-bootstrap5 fv-plugins-framework flex flex-col gap-5" method="POST"
                    action="{{ route('admin:setting.tripay.update') }}">
                    @method('PUT')
                    @csrf

                    <x-form.group.select name="tripay_environment" required :value="@$tripay['tripay_environment']"
                        label="Environment">
                        <option value="sandbox">Sandbox</option>
                        <option value="production">Production</option>
                    </x-form.group.select>

                    <x-form.group.input name="tripay_api_key" required :value="@$tripay['tripay_api_key']" label="API Key"
                        placeholder="your_tripay_api_key">
                        <x-slot:description>
                            <a href="https://tripay.co.id/developer/api">https://tripay.co.id/developer/api</a>
                        </x-slot:description>
                    </x-form.group.input>

                    <x-form.group.input name="tripay_private_key" required :value="@$tripay['tripay_private_key']"
                        label="Private Key" placeholder="your_tripay_private_key">
                        <x-slot:description>
                            <a href="https://tripay.co.id/developer/api">https://tripay.co.id/developer/api</a>
                        </x-slot:description>
                    </x-form.group.input>

                    <x-form.group.input name="tripay_merchant_code" required :value="@$tripay['tripay_merchant_code']"
                        label="Merchant Code" placeholder="your_tripay_merchant_code">
                        <x-slot:description>
                            <a href="https://tripay.co.id/developer/api">https://tripay.co.id/developer/api</a>
                        </x-slot:description>
                    </x-form.group.input>

                    <x-form.group.input name="tripay_callback_url" disabled value="{{ url('/tripay/callback') }}"
                        label="Callback URL">
                        <x-slot:description>
                            <a href="https://tripay.co.id/developer/api">https://tripay.co.id/developer/api</a>
                        </x-slot:description>
                    </x-form.group.input>

                    <x-form.row>
                        <x-form.label label="Channels" />
                        <div class="col-md-9 pt-5">
                            <div class="row g-2">
                                @foreach ($channels as $channel)
                                    <div class="form-check col-4 col-md-3">
                                        <input class="form-check-input" type="checkbox"
                                            @checked(in_array($channel['id'],@$tripay['tripay_channels']))
                                            id="tripay_channel{{ $channel['id'] }}" name="tripay_channels[]"
                                            value="{{ $channel['id'] }}"/>
                                        <label class="form-check-label" for="tripay_channel{{ $channel['id'] }}">
                                            {{ $channel['name'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </x-form.row>

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
    @push('addon-script')
        <script>
            window.tripayForm = () => ({
                init() {
                    console.log("Tripay settings initialized.");
                }
            });
        </script>
    @endpush
</x-admin-layout>
