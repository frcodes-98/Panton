@props([
    'name',
    'title' => null,
    'titleExpr' => null,
    'submit' => null,
    'danger' => false,
    'width' => 'max-w-md',
])

@php
    $headClass = $danger
        ? 'bg-gradient-to-r from-rose-500 to-pink-600 px-6 py-4 text-white'
        : 'modal-head';
    $cardClass = 'modal-card '.$width;
@endphp

<div x-show="modal === '{{ $name }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="modal = null">
    <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" @click="modal = null"></div>
    @if ($submit)
        <form class="{{ $cardClass }}" @submit.prevent="{{ $submit }}">
    @else
        <div class="{{ $cardClass }}">
    @endif
        <div class="flex items-center justify-between gap-4 {{ $headClass }}">
            @if ($titleExpr)
                <h2 class="text-lg font-semibold" x-text="{{ $titleExpr }}"></h2>
            @else
                <h2 class="text-lg font-semibold">{{ $title }}</h2>
            @endif
            <button type="button" class="text-2xl leading-none text-white/80 hover:text-white" @click="modal = null" aria-label="Close">×</button>
        </div>
        <div class="space-y-3 p-6">
            {{ $slot }}
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
            {{ $footer }}
        </div>
    @if ($submit)
        </form>
    @else
        </div>
    @endif
</div>
