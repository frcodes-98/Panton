@extends('layouts.base')

@section('body-class', 'bg-slate-100 text-slate-800 antialiased')

@section('content')
    @php
        $navigation = [
            ['route' => 'system.welcome', 'match' => 'system.welcome', 'label' => 'Welcome', 'hint' => 'System Control', 'icon' => 'home'],
            ['route' => 'system.users.index', 'match' => 'system.users.*', 'label' => 'User Management', 'hint' => 'Add / edit users', 'icon' => 'users'],
            ['route' => 'system.groups', 'match' => 'system.groups', 'label' => 'Group Management', 'hint' => 'Add / edit groups', 'icon' => 'group'],
            ['route' => 'system.workspaces.index', 'match' => 'system.workspaces.*', 'label' => 'Areas', 'hint' => 'Add more areas', 'icon' => 'workspace'],
            ['route' => 'system.modules', 'match' => 'system.modules', 'label' => 'Module Configuration', 'hint' => 'Manage module', 'icon' => 'module'],
            ['route' => 'system.widgets.index', 'match' => 'system.widgets.*', 'label' => 'Widget Configuration', 'hint' => 'Add / edit widget', 'icon' => 'widget'],
            ['route' => 'system.settings', 'match' => 'system.settings', 'label' => 'System Configuration', 'hint' => 'Add / edit system settings', 'icon' => 'settings'],
            ['route' => 'system.marquee', 'match' => 'system.marquee', 'label' => 'Marquee Configuration', 'hint' => 'Configure marquee', 'icon' => 'marquee'],
        ];
        $user = session('demo_user');
    @endphp

    <div x-data="{ navOpen: false }" class="min-h-screen lg:grid lg:grid-cols-[272px_1fr]">
        <div x-show="navOpen" x-cloak class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="navOpen = false"></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-gradient-to-b from-teal-800 via-teal-900 to-cyan-950 text-white shadow-2xl transition lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:w-auto lg:translate-x-0"
            :class="navOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between px-5 py-5">
                <a href="{{ route('system.welcome') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-lg font-bold text-teal-800 shadow-lg">P</span>
                    <span>
                        <span class="block font-semibold leading-tight">System Control</span>
                        <span class="block text-[11px] uppercase tracking-[0.2em] text-teal-200">Panton</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 pb-4">
                @foreach ($navigation as $item)
                    @php $active = request()->routeIs($item['match']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition {{ $active ? 'bg-white text-teal-900 shadow-lg shadow-teal-950/30' : 'text-teal-50/90 hover:bg-white/10' }}"
                    >
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $active ? 'bg-gradient-to-br from-teal-500 to-cyan-600 text-white' : 'bg-white/10 text-teal-100 group-hover:bg-white/15' }}">
                            <x-icon :name="$item['icon']" class="h-[18px] w-[18px]" />
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-medium">{{ $item['label'] }}</span>
                            <span class="block truncate text-xs {{ $active ? 'text-teal-700' : 'text-teal-200/80' }}">{{ $item['hint'] }}</span>
                        </span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-4">
                <a href="{{ route('desktop') }}" class="flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-3 py-2.5 text-sm font-medium hover:bg-white/15">
                    <x-icon name="home" class="h-4 w-4" />
                    Back to desktop
                </a>
            </div>
        </aside>

        <div class="min-w-0">
            <div
                x-data="{ text: null, init() { try { const s = JSON.parse(sessionStorage.getItem('panton.marquee') || 'null'); if (s?.flag === 'on' && s.text) this.text = s.text; } catch {} } }"
                x-show="text"
                x-cloak
                class="overflow-hidden bg-amber-300 py-1.5 text-sm font-medium text-amber-950"
                @marquee-updated.window="text = $event.detail.flag === 'on' ? $event.detail.text : null"
            >
                <p class="animate-marquee whitespace-nowrap" x-text="text"></p>
            </div>

            <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/85 backdrop-blur">
                <div class="flex items-center justify-between gap-4 px-4 py-3.5 sm:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="btn btn-secondary px-2.5 lg:hidden" @click="navOpen = true">Menu</button>
                        <div>
                            <p class="flex items-center gap-1 text-xs font-medium text-slate-500">
                                <a href="{{ route('system.welcome') }}" class="hover:text-teal-700">System Control</a>
                                <x-icon name="chevron" class="h-3 w-3" />
                                <span class="text-teal-700">@yield('heading')</span>
                            </p>
                            <h1 class="text-lg font-semibold text-slate-900">@yield('heading')</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium text-slate-800">{{ strtoupper($user) }}</p>
                            <p class="text-xs text-slate-500">Admin</p>
                        </div>
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-amber-300 to-orange-400 text-sm font-bold text-orange-950">{{ strtoupper(substr($user, 0, 1)) }}</span>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl p-4 sm:p-8">
                @yield('main')
            </main>
        </div>
    </div>
@endsection
