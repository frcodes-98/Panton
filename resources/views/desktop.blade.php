@extends('layouts.base')

@section('title', 'Desktop · Panton')

@section('body-class', 'min-h-screen bg-teal-950 text-white antialiased')

@section('content')
    @php
        $user = session('demo_user');
        $hour = (int) now()->format('G');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $areas = array_values($tiles);
        $palette = [
            'bg-teal-400/20 text-teal-100 ring-teal-300/30',
            'bg-cyan-400/20 text-cyan-100 ring-cyan-300/30',
            'bg-sky-400/20 text-sky-100 ring-sky-300/30',
            'bg-emerald-400/20 text-emerald-100 ring-emerald-300/30',
            'bg-amber-300/20 text-amber-100 ring-amber-200/30',
            'bg-rose-400/20 text-rose-100 ring-rose-300/30',
            'bg-violet-400/20 text-violet-100 ring-violet-300/30',
        ];
        $href = fn ($tile) => $tile['slug'] === 'system-control'
            ? route('system.welcome')
            : route('workspace.placeholder', $tile['slug']);
    @endphp

    <div
        x-data="{
            q: '',
            marquee: null,
            known: {{ Js::from(array_column($areas, 'name')) }},
            init() {
                try {
                    const saved = JSON.parse(sessionStorage.getItem('panton.marquee') || 'null');
                    if (saved?.flag === 'on' && saved.text) this.marquee = saved.text;
                } catch {}
            },
            matches(name) { return name.toLowerCase().includes(this.q.trim().toLowerCase()); },
            noMatch() {
                return this.q.trim() !== '' && !this.known.some((name) => this.matches(name));
            },
        }"
        class="relative min-h-screen overflow-hidden bg-gradient-to-br from-teal-800 via-teal-900 to-cyan-950"
    >
        <div class="pointer-events-none absolute -left-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-teal-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-32 top-1/3 h-[28rem] w-[28rem] rounded-full bg-cyan-400/15 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>

        <div x-show="marquee" x-cloak class="relative z-10 overflow-hidden bg-amber-300 py-1.5 text-sm font-medium text-amber-950">
            <p class="animate-marquee whitespace-nowrap" x-text="marquee"></p>
        </div>

        <div class="relative z-10 mx-auto max-w-6xl px-5 pb-32 pt-6 sm:px-8">
            <header class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-lg font-bold text-teal-800 shadow-lg">P</span>
                    <div>
                        <p class="font-semibold leading-tight">Panton</p>
                        <p class="text-[11px] uppercase tracking-[0.25em] text-teal-200">On your side</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium">{{ strtoupper($user) }}</p>
                        <p class="text-xs text-teal-200">Admin</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-amber-300 to-orange-400 text-sm font-bold text-orange-950 ring-2 ring-white/30">{{ strtoupper(substr($user, 0, 1)) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15 hover:bg-white/20" title="Log out">
                            <x-icon name="logout" class="h-4 w-4" />
                            <span class="sr-only">Log out</span>
                        </button>
                    </form>
                </div>
            </header>

            <section class="mt-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-medium text-teal-200">{{ now()->format('l, j F Y') }}</p>
                    <h1 class="mt-1 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $greeting }}, {{ ucfirst(explode('.', $user)[1] ?? $user) }}</h1>
                    <p class="mt-2 text-teal-100/80">Signed in as Admin. Open an area, or add another.</p>
                </div>
                <label class="relative w-full md:w-80">
                    <span class="sr-only">Search areas</span>
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-teal-200" />
                    <input x-model="q" type="search" placeholder="Search areas" class="w-full rounded-2xl border border-white/15 bg-white/10 py-3 pl-10 pr-4 text-sm text-white placeholder:text-teal-100/60 outline-none backdrop-blur focus:border-white/40 focus:bg-white/15" />
                </label>
            </section>

            <section class="mt-10">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-200">Areas</h2>
                    <a href="{{ route('system.workspaces.create') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white ring-1 ring-white/15 hover:bg-white/20">
                        <x-icon name="plus" class="h-3.5 w-3.5" /> Add area
                    </a>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($areas as $index => $tile)
                        <a
                            href="{{ $href($tile) }}"
                            x-show="matches(@js($tile['name']))"
                            class="group flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.07] p-3.5 backdrop-blur transition hover:border-white/25 hover:bg-white/[0.14]"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 {{ $palette[$index % count($palette)] }}">
                                <x-icon :name="$tile['icon']" class="h-5 w-5" />
                            </span>
                            <span class="min-w-0 text-sm font-medium leading-snug text-white/95">{{ $tile['name'] }}</span>
                        </a>
                    @endforeach
                </div>
                <p x-show="noMatch()" x-cloak class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-sm text-teal-100">
                    No area matches that search.
                </p>
            </section>
        </div>

        <div x-data="{ open: false, visible: true }" x-show="visible" x-cloak class="fixed bottom-5 right-5 z-20 w-80 max-w-[calc(100vw-2rem)]">
            <div class="relative flex gap-3 rounded-2xl bg-white p-4 text-slate-800 shadow-2xl ring-1 ring-amber-200">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <x-icon name="bell" class="h-5 w-5" />
                </span>
                <div class="pr-4">
                    <p class="text-sm font-medium">{{ count($notices) }} versions are about to become obsolete.</p>
                    <button type="button" class="mt-1 text-sm font-semibold text-teal-700 hover:text-teal-900" @click="open = true">Click here to view</button>
                </div>
                <button type="button" class="absolute right-3 top-2 text-slate-400 hover:text-slate-700" @click="visible = false" aria-label="Dismiss notice">×</button>
            </div>

            <div x-show="open" x-cloak class="fixed inset-0 z-30 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="open = false"></div>
                <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white text-slate-800 shadow-2xl">
                    <div class="bg-gradient-to-r from-amber-400 to-orange-500 px-6 py-5 text-white">
                        <h2 class="text-lg font-semibold">Versions about to become obsolete</h2>
                        <p class="text-sm text-white/85">Review these before they expire.</p>
                    </div>
                    <ul class="divide-y divide-slate-100 px-6">
                        @foreach ($notices as $notice)
                            <li class="flex items-center justify-between py-4 text-sm">
                                <span>
                                    <span class="block font-medium">{{ $notice['name'] }}</span>
                                    <span class="text-slate-500">Version {{ $notice['version'] }}</span>
                                </span>
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800">{{ $notice['expires'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="flex justify-end px-6 pb-6">
                        <button type="button" class="btn btn-primary" @click="open = false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
