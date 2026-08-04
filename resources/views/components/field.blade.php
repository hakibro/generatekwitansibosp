@props(['name', 'label', 'value' => '', 'type' => 'text', 'class' => ''])

<div class="field {{ $class }}">
    <label>{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}"
        value="{{ old(str_replace(['[', ']'], ['.', ''], $name), $value) }}">
</div>
