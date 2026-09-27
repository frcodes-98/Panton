@props(['number', 'title', 'hint' => null, 'optional' => false])

<section class="panel overflow-hidden transition" :class="openStep === {{ $number }} ? 'ring-2 ring-teal-500/40' : ''">
    <button type="button" class="flex w-full items-center gap-4 px-5 py-4 text-left" @click="openStep = openStep === {{ $number }} ? 0 : {{ $number }}">
        <span
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-sm font-semibold transition"
            :class="openStep === {{ $number }} ? 'bg-gradient-to-br from-teal-500 to-cyan-700 text-white shadow-md shadow-teal-700/30' : 'bg-teal-50 text-teal-800'"
        >{{ $number }}</span>
        <span class="min-w-0 flex-1">
            <span class="flex flex-wrap items-center gap-2">
                <span class="font-semibold text-slate-900">{{ $title }}</span>
                @if ($optional)
                    <span class="pill bg-amber-50 text-amber-800 ring-1 ring-amber-200">Optional</span>
                @endif
            </span>
            @if ($hint)
                <span class="mt-0.5 block text-sm text-slate-500">{{ $hint }}</span>
            @endif
        </span>
        @isset($summary)
            <span class="hidden text-sm text-slate-500 sm:block">{{ $summary }}</span>
        @endisset
        <x-icon name="chevron" class="h-5 w-5 shrink-0 text-slate-400 transition" x-bind:class="openStep === {{ $number }} ? 'rotate-90 text-teal-600' : ''" />
    </button>
    <div x-show="openStep === {{ $number }}" x-transition.opacity class="border-t border-slate-100 bg-slate-50/40 p-5">
        {{ $slot }}
    </div>
</section>
