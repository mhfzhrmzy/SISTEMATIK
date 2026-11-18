@props(['padding' => 'p-8'])

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl shadow-xs $padding"]) }}>
    {{ $slot }}
</div>
