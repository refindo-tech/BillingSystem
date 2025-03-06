@php
    $action = $mode == 'edit' ? route('admin:prepaid.user.update', $user) : route('admin:prepaid.user.store');
    $method = $mode == 'edit' ? 'PATCH' : 'POST';
    $activeMenu = $mode == 'edit' ? 'prepaid.user.edit' : 'prepaid.user.create';
@endphp
<x-admin-layout title="Recharge Account" :active-menu="$activeMenu" :path="['List Prepaid User' => route('admin:prepaid.user.index'), 'Recharge Account' => '']">
    <div class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body" x-data="rechargeUserForm()" x-init="init()">

                <!--begin::Form-->
                <form class="form fv-plugins-bootstrap5 fv-plugins-framework flex flex-col gap-5" method="POST"
                    action="{{ $action }}">
                    @method($method)
                    @csrf
                    @if ($mode == 'edit')
                        <input type="hidden" name="id" value="{{ $user['id'] }}" />
                    @endif
                    <x-form.group.select name="customer_id" label="Select Account" :options="$customers" required
                        :value="@$user['customer_id']" :readonly="$mode == 'edit'" />
                    <x-form.group.input name="service_number" label="Nomor Layanan" required :readonly="$mode == 'edit'"
                        tooltip="2 digit tahun + ID pelanggan + 2 digit urutan layanan by pelanggan."
                        :value="@$user['service_number'] ?? ''" />

                    {{-- Payment channel --}}
                    <x-form.group.select name="payment_channel" label="Payment Channel" :options="$activeChannels" required
                        :value="@$user['payment_channel']" />


                    <hr>

                    <x-form.group.select name="plan_type" label="Type" :options="$planTypes" required :value="@$user['plan_type'] ?? @$defaultPlanType?->value"
                        :readonly="$mode == 'edit'" />

                    <x-form.group.select name="router_id" label="Routers" :options="[]" required :value="@$user['router_id'] ?? @$defaultRouterId"
                        :readonly="$mode == 'edit'" />

                    <x-form.group.select name="server_id" label="Server" :options="[]" required :value="@$user['server_id']"
                        :readonly="$mode == 'edit'" />

                    <x-form.group.input name="username" label="Username" required :value="@$user['username'] ?? ''" />
                    <x-form.group.input name="pppoe_password" label="Password" required value="1234"
                        :value="@$user['pppoe_password'] ?? '123456'" />

                    <hr>

                    <x-form.group.select name="plan_id" label="Service Plan" :options="[]" required
                        :value="@$user['plan_id']" />

                    <x-form.group.select name="validity_cycle" label="Siklus Layanan" :options="$validityCycles" required
                        tooltip=" Profile: Mengikuti durasi dasar masing-masing paket. Tetap: Tenggat kadaluarsa mengikuti tanggal aktivasi paket. Bulanan: Selalu berakhir di tanggal yang sama, yaitu tiap tanggal 4 tiap bulan." />

                    @if ($mode == 'edit')
                        <x-form.group.input :disabled="true" name="Tanggal " type="datetime-local" :value="@$user['created_at']->format('Y-m-d H:i') ?? ''"
                            label="Activation Date" />
                        <x-form.group.input name="expired_at" type="datetime-local" required :value="@$user['created_at']->format('Y-m-d H:i') ?? ''"
                            label="Expired Date" />
                    @else
                        <x-form.group.input name="active_at" type="datetime-local" required label="Activation Date"
                            value="{{ now()->format('Y-m-d\TH:i') }}" />
                        <x-form.group.input name="expired_at" type="datetime-local" required label="Expired Date"
                            readonly />
                    @endif

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
            window.rechargeUserForm = () => ({
                cache: {
                    routers: {},
                    plans: {},
                    servers: {},
                    serviceNumbers: {},
                },

                async fetchData(url, cacheKey, param) {
                    if (this.cache[cacheKey] && this.cache[cacheKey][param]) {
                        return this.cache[cacheKey][param]; // Return cached response
                    }

                    const response = await fetch(url);
                    const data = await response.json();
                    this.cache[cacheKey][param] = data; // Store in cache
                    return data;
                },

                async getRouter(planType, defaultValue) {
                    const data = await this.fetchData("{{ route('admin:network.router.option') }}?plan_type=" +
                        planType, "routers", planType);

                    this.populateSelect('[name="router_id"]', data, defaultValue);
                    this.getPlan($('[name="router_id"]').val(), @json($user['plan_id'] ?? ''));
                },

                async getPlan(routerId, defaultValue) {
                    if (!routerId) return;
                    const planType = $('[name="plan_type"]').val();
                    const data = await this.fetchData("{{ route('admin:network.plan.option') }}?router_id=" +
                        routerId + "&plan_type=" + planType, "plans", routerId);

                    this.populateSelect('[name="plan_id"]', data, defaultValue);
                },

                async getServer(routerId, defaultValue) {
                    if (!routerId) return;
                    const data = await this.fetchData("{{ route('admin:network.server.option') }}?router_id=" +
                        routerId, "servers", routerId);

                    this.populateSelect('[name="server_id"]', data, defaultValue);
                },

                async updateServiceNumber(customerId) {
                    if (!customerId || this.cache.serviceNumbers[customerId]) {
                        $('[name="service_number"]').val(this.cache.serviceNumbers[customerId] || '');
                        $('[name="username"]').val((this.cache.serviceNumbers[customerId] || '') + '@netplus.id');
                        return;
                    }

                    const data = await this.fetchData(
                        "{{ route('admin:prepaid.user.service-number') }}?customer_id=" + customerId,
                        "serviceNumbers", customerId);

                    $('[name="service_number"]').val(data);
                    $('[name="username"]').val(data + '@netplus.id');
                },

                async updateExpiredAt() {
                    const validityCycle = $('[name="validity_cycle"]').val();
                    const activeAt = $('[name="active_at"]').val();
                    const planId = $('[name="plan_id"]').val();

                    if (!validityCycle || !activeAt || !planId) return;

                    const url =
                        `{{ route('admin:prepaid.user.expired-at') }}?validity_cycle=${validityCycle}&active_at=${activeAt}&plan_id=${planId}`;
                    const response = await fetch(url);
                    const data = await response.json();

                    $('[name="expired_at"]').val(data);
                },

                populateSelect(selector, data, defaultValue) {
                    const $select = $(selector);
                    $select.empty().select2({
                        data: [{
                            id: "",
                            text: ""
                        }, ...Object.entries(data).map(([key, value]) => ({
                            id: key,
                            text: value
                        }))]
                    }).val(defaultValue).trigger('change');
                },

                init() {
                    this.getRouter($('[name="plan_type"]').val(), @json(@$defaultRouterId ?? ''));

                    $('[name="plan_type"]').on('change', (e) => this.getRouter(e.target.value));
                    $('[name="router_id"]').on('change', (e) => {
                        this.getServer(e.target.value);
                        this.getPlan(e.target.value);
                    });
                    $('[name="customer_id"]').on('change', (e) => {
                        if (!$('[name="service_number"]').prop('readonly')) {
                            this.updateServiceNumber(e.target.value);
                        }
                    });

                    // Debounce updateExpiredAt calls
                    let timeout;
                    $('[name="validity_cycle"], [name="active_at"], [name="plan_id"]').on('change', () => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => this.updateExpiredAt(), 300);
                    });

                    // Auto-update username when service_number changes
                    $('[name="service_number"]').on('change', (e) => {
                        $('[name="username"]').val(e.target.value + '@netplus.id');
                    });

                    //init service number if customer_id is set
                    if ($('[name="customer_id"]').val()) {
                        this.updateServiceNumber($('[name="customer_id"]').val());
                    }
                },
            });
        </script>
    @endpush
</x-admin-layout>
