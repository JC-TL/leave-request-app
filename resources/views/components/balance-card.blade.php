@props(['balance'])

@php
    $available = $balance->getAvailableBalance();
    $total = $balance->balance;
    $percentage = $total > 0 ? ($available / $total) * 100 : 0;
    
    if ($available == 0) {
        $bgColor = 'bg-base-300';
        $textColor = 'text-base-content/40';
    } elseif ($percentage > 50) {
        $bgColor = 'bg-success/20';
        $textColor = 'text-base-content';
    } elseif ($percentage >= 10) {
        $bgColor = 'bg-warning/20';
        $textColor = 'text-base-content';
    } else {
        $bgColor = 'bg-error/20';
        $textColor = 'text-base-content';
    }
@endphp

<div class="card {{ $bgColor }} shadow-sm border border-base-300">
    <div class="card-body p-4">
        <h3 class="card-title text-sm font-semibold {{ $textColor }}">{{ $balance->leave_type }}</h3>
        <p class="text-2xl font-bold {{ $textColor }}">{{ number_format($available, 1) }} / {{ number_format($total, 1) }}</p>
        <p class="text-xs {{ $textColor }}/70">Available / Total</p>
    </div>
</div>

