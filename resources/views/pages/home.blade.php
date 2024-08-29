<x-layout.app title="Home">
    <x-ui.hero title="Saatnya menikmati layanan akses internet super cepat!!" description="Menjangkau Semua Kalangan Masyarakat Untuk Mendapatkan Akses Internet Hebat, Harga Hemat.">
        <x-slot name="image">
            <img src="{{ asset('images/hero/hero.png') }}" alt="hero">
        </x-slot>
    </x-ui.hero>
    <x-ui.package title="Sensasi internetan ngebut seharian!" description="Layanan ini cocok buat kamu yang hobi streaming, hiburan keluarga dan juga untuk kerjaan online. Pilih Layananmu, Sesuaikan kebutuhanmu. Daftar Sekarang!"/>
    <x-ui.service />
    <x-ui.testimony />
    <x-ui.hero2/>

    <x-ui.feature/>

    <x-ui.partner />

    <x-ui.contact />

</x-layout.app>
