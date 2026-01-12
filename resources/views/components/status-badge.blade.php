@props(['status'])

@php
    $colors = [
        'pending' => 'badge-warning',
        'dept_manager_approved' => 'badge-info',
        'dept_manager_rejected' => 'badge-error',
        'hr_approved' => 'badge-success',
        'hr_rejected' => 'badge-error',
    ];
    
    $labels = [
        'pending' => 'Pending',
        'dept_manager_approved' => 'Manager Approved',
        'dept_manager_rejected' => 'Manager Rejected',
        'hr_approved' => 'Approved',
        'hr_rejected' => 'Rejected',
    ];
    
    $color = $colors[$status] ?? 'badge-neutral';
    $label = $labels[$status] ?? 'Unknown';
@endphp

<span class="badge {{ $color }}">{{ $label }}</span>

