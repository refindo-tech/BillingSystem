@php
    $label = match($status) {
        'open' => 'OPEN',
        'closed' => 'CLOSED',
        'pending' => 'PENDING',
        default => 'Unknown'
    };
    $color = match($status) {
        'open' => 'light-success',
        'closed' => 'light-danger',
        'pending' => 'light-warning',
        default => 'light-secondary'
    };
@endphp

<span class="badge badge-lg badge-{{ $color }}">
    {{ $label }}
</span>