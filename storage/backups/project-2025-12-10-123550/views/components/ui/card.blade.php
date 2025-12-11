@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footer' => null,
    'shadow' => 'md',
    'bordered' => true
])

@php
    $cardClasses = 'card shadow-' . $shadow;
    if (!$bordered) {
        $cardClasses .= ' border-0';
    }
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($title)
        <div class="card-header {{ $headerClass }}">
            <h5 class="card-title mb-0">{{ $title }}</h5>
            @if($subtitle)
                <p class="card-subtitle text-muted mb-0">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>