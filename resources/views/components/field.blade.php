@props(['name', 'label', 'value' => '', 'class' => ''])

<div class="field {{ $class }}">
    <label>{{ $label }}</label>
    <input name="{{ $name }}" value="{{ old(str_replace(['[', ']'], ['.', ''], $name), $value) }}">
</div>
