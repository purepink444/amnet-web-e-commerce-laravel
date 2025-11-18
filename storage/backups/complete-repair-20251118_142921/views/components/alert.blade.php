@props(['type' => 'info', 'dismissible' => false])

@php
    $alertClass = "alert alert-$type";
    if ($dismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }
@endphp

<div {{ $attributes->merge(['class' => $alertClass]) }} role="alert">
    {{ $slot }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
