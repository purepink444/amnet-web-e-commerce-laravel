@props([
    'type' => 'text',
    'name' => '',
    'value' => '',
    'label' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'icon' => null,
    'size' => 'md'
])

@php
    $inputId = $name ?: 'input-' . uniqid();
    $hasError = $error || ($name && $errors->has($name));
    $errorMessage = $error ?: ($name ? $errors->first($name) : null);

    $inputClass = 'form-control';
    if ($hasError) {
        $inputClass .= ' is-invalid';
    }

    $sizeClasses = [
        'sm' => 'form-control-sm',
        'md' => '',
        'lg' => 'form-control-lg'
    ];

    $inputClass .= ' ' . ($sizeClasses[$size] ?? '');
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group {{ $icon ? 'has-validation' : '' }}">
        @if($icon)
            <span class="input-group-text">
                <i class="{{ $icon }}" aria-hidden="true"></i>
            </span>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="{{ $inputClass }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->except(['class']) }}
        >

        @if($hasError)
            <div class="invalid-feedback d-block">
                {{ $errorMessage }}
            </div>
        @endif
    </div>

    @if($help && !$hasError)
        <div class="form-text">
            {{ $help }}
        </div>
    @endif
</div>