@php
    $label = match($status) {
        'off' => 'Belum Aktif',
        'on' => 'Aktif',
        default => 'Unknown'
    };
    $color = match($status) {
        'off' => 'light-danger',
        'on' => 'light-success',
        default => 'light-secondary'
    };
@endphp

<span class="badge badge-lg badge-{{ $color }}">
    {{ $label }}
</span>