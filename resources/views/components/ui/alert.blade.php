@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null,
    'title' => null
])

@php
    $alertClass = "alert alert-{$type}";
    if ($dismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }

    $icons = [
        'success' => 'check-circle',
        'error' => 'exclamation-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'info-circle'
    ];

    $defaultIcon = $icons[$type] ?? 'info-circle';
    $iconClass = $icon ?: $defaultIcon;
@endphp

<div {{ $attributes->merge(['class' => $alertClass, 'role' => 'alert']) }}>
    @if($icon || $title)
        <div class="d-flex align-items-start">
            @if($icon !== false)
                <i class="fas fa-{{ $iconClass }} me-2 mt-1 flex-shrink-0" aria-hidden="true"></i>
            @endif

            <div class="flex-grow-1">
                @if($title)
                    <strong>{{ $title }}</strong>
                    @if($slot->isNotEmpty())<br>@endif
                @endif
                {{ $slot }}
            </div>
        </div>
    @else
        {{ $slot }}
    @endif

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="ปิดการแจ้งเตือน"></button>
    @endif
</div>