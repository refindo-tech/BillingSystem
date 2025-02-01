@php
    $label = match($priority) {
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        default => 'Unknown'
    };
    $color = match($priority) {
        'low' => 'light-info',
        'medium' => 'light-warning',
        'high' => 'light-danger',
        default => 'light-secondary'
    };
@endphp

<span class="badge badge-lg badge-{{ $color }}">
    {{ $label }}
</span>