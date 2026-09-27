@extends('layouts.system')

@section('title', 'System Control · Panton')

@section('heading', 'Welcome')

@section('main')
    @php
        $tiles = [
            ['route' => 'system.users.index', 'label' => 'User Management', 'hint' => 'Add and edit users, their groups, workspaces and widgets', 'icon' => 'users', 'color' => 'from-teal-400 to-cyan-600'],
            ['route' => 'system.groups', 'label' => 'Group Management', 'hint' => 'Organise users into access groups', 'icon' => 'group', 'color' => 'from-sky-400 to-indigo-600'],
            ['route' => 'system.workspaces.index', 'label' => 'Workspace Management', 'hint' => 'Create workspaces, approvers and document control', 'icon' => 'workspace', 'color' => 'from-emerald-400 to-teal-600'],
            ['route' => 'system.modules', 'label' => 'Module Configuration', 'hint' => 'Install, edit, enable and disable modules', 'icon' => 'module', 'color' => 'from-violet-400 to-purple-600'],
            ['route' => 'system.widgets.index', 'label' => 'Widget Configuration', 'hint' => 'Build widgets and assign them to people', 'icon' => 'widget', 'color' => 'from-lime-400 to-emerald-600'],
            ['route' => 'system.settings', 'label' => 'System Configuration', 'hint' => 'Application settings and values', 'icon' => 'settings', 'color' => 'from-slate-400 to-slate-600'],
            ['route' => 'system.marquee', 'label' => 'Marquee', 'hint' => 'Scrolling announcement across the top', 'icon' => 'marquee', 'color' => 'from-amber-300 to-orange-500'],
        ];
        $stats = [
            ['label' => 'Users', 'value' => $counts['users'], 'icon' => 'users', 'tone' => 'bg-teal-50 text-teal-700'],
            ['label' => 'Groups', 'value' => $counts['groups'], 'icon' => 'group', 'tone' => 'bg-sky-50 text-sky-700'],
            ['label' => 'Workspaces', 'value' => $counts['workspaces'], 'icon' => 'workspace', 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Modules', 'value' => $counts['modules'], 'icon' => 'module', 'tone' => 'bg-violet-50 text-violet-700'],
        ];
    @endphp

    <section class="relative overflow-hidden rounded-3xl bg-teal-900 text-white shadow-xl shadow-teal-900/20">
        <img src="{{ asset('images/panton-hero.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover object-[center_75%] opacity-40" />
        <div class="absolute inset-0 bg-gradient-to-r from-teal-900 via-teal-900/85 to-cyan-800/40"></div>
        <div class="relative px-6 py-10 sm:px-10">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-teal-200">System Control</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight">Welcome to System Control</h2>
            <p class="mt-2 max-w-xl text-teal-50/85">Manage who can sign in, what they can see, and how Panton behaves.</p>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="panel flex items-center gap-4 p-5">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $stat['tone'] }}">
                    <x-icon :name="$stat['icon']" class="h-6 w-6" />
                </span>
                <div>
                    <p class="text-2xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </section>

    <section class="mt-8">
        <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Manage</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($tiles as $tile)
                <a href="{{ route($tile['route']) }}" class="panel group flex items-start gap-4 p-5 transition hover:-translate-y-0.5 hover:shadow-lg hover:ring-teal-200">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-md {{ $tile['color'] }}">
                        <x-icon :name="$tile['icon']" class="h-6 w-6" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center justify-between gap-2 font-semibold text-slate-900">
                            {{ $tile['label'] }}
                            <x-icon name="chevron" class="h-4 w-4 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-teal-600" />
                        </span>
                        <span class="mt-1 block text-sm text-slate-500">{{ $tile['hint'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endsection
