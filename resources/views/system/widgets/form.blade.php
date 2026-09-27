@extends('layouts.system')

@section('title', ($mode === 'edit' ? 'Edit widget' : 'Add widget').' · Panton')

@section('heading', $mode === 'edit' ? 'Edit widget' : 'Add widget')

@section('main')
    @php
        $steps = [
            1 => ['Widget properties', 'Name and maximum size', false, null],
            2 => ['Please select one module', 'The module this widget shows', false, 'modulePicks'],
            3 => ['Please select workspace', 'Limit the widget to one workspace', true, 'workspacePicks'],
            4 => ['Assign user to widget', 'People who see this widget', true, 'userPicks'],
            5 => ['Assign group to widget', 'Groups who see this widget', true, 'groupPicks'],
        ];
    @endphp

    <div x-data="widgetForm(@js([
        'mode' => $mode,
        'widgetId' => $widgetId,
        'indexUrl' => route('system.widgets.index'),
        'widgets' => $widgets,
    ]))" x-cloak class="mx-auto max-w-5xl">
        <a href="{{ route('system.widgets.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-teal-700 hover:text-teal-900">
            <x-icon name="chevron" class="h-4 w-4 rotate-180" /> Back to widgets
        </a>

        <div class="mb-5 flex items-center gap-2">
            @foreach ($steps as $number => $step)
                <button type="button" class="h-1.5 flex-1 rounded-full transition" :class="openStep >= {{ $number }} ? 'bg-gradient-to-r from-teal-500 to-cyan-600' : 'bg-slate-200'" @click="openStep = {{ $number }}" title="{{ $step[0] }}"></button>
            @endforeach
        </div>

        <p x-show="error" class="mb-4 flex items-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            <span class="font-semibold">Check this:</span> <span x-text="error"></span>
        </p>
        <div x-show="missing" class="panel p-6">
            <a href="{{ route('system.widgets.index') }}" class="btn btn-primary">Back to widgets</a>
        </div>

        <form x-show="!missing" class="space-y-3" @submit.prevent="save()">
            @foreach ($steps as $number => [$title, $hint, $optional, $pickModel])
                <x-wizard-step :number="$number" :title="$title" :hint="$hint" :optional="$optional">
                    @if ($number === 1)
                        <div class="grid gap-5 md:grid-cols-[1fr_16rem]">
                            <div class="space-y-4 rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                                <div>
                                    <label class="label" for="widget-name">Widget name</label>
                                    <input id="widget-name" x-model="name" class="input" />
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="label" for="widget-height">Widget height</label>
                                        <div class="relative">
                                            <input id="widget-height" type="number" min="1" x-model="height" class="input pr-10" />
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">px</span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Set maximum height</p>
                                    </div>
                                    <div>
                                        <label class="label" for="widget-width">Widget width</label>
                                        <div class="relative">
                                            <input id="widget-width" type="number" min="1" x-model="width" class="input pr-10" />
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">px</span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">Set maximum width</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center justify-center rounded-2xl bg-gradient-to-br from-teal-50 to-cyan-50 p-5 ring-1 ring-teal-100">
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-teal-700">Preview</p>
                                <div
                                    class="flex max-h-40 max-w-full items-center justify-center rounded-xl bg-white text-xs text-slate-500 shadow-md ring-1 ring-teal-200"
                                    :style="`width: ${Math.min(Number(width) || 180, 1200) / 6}px; height: ${Math.min(Number(height) || 120, 800) / 6}px; min-width: 48px; min-height: 32px;`"
                                >
                                    <span x-text="(width || '?') + ' × ' + (height || '?')"></span>
                                </div>
                                <p class="mt-3 max-w-full truncate text-sm font-medium text-slate-700" x-text="name || 'Untitled widget'"></p>
                            </div>
                        </div>
                    @elseif ($number === 2)
                        <x-picker :items="$modules" :columns="[['key' => 'name', 'label' => 'Module name'], ['key' => 'description', 'label' => 'Description']]" model="modulePicks" storage-key="panton.modules" :single="true" title="Select one module" />
                    @elseif ($number === 3)
                        <x-picker :items="$workspaces" :columns="[['key' => 'name', 'label' => 'Workspace']]" model="workspacePicks" storage-key="panton.workspaces" :single="true" title="Select workspace" />
                    @elseif ($number === 4)
                        <x-picker :items="$users" :columns="[['key' => 'username', 'label' => 'Username'], ['key' => 'full_name', 'label' => 'Full name'], ['key' => 'department', 'label' => 'Department']]" model="userPicks" storage-key="panton.users" title="Assign users" />
                    @else
                        <x-picker :items="$groups" :columns="[['key' => 'name', 'label' => 'Group name'], ['key' => 'total_users', 'label' => 'Total users']]" model="groupPicks" storage-key="panton.groups" title="Assign groups" />
                    @endif
                    @if ($number < count($steps))
                        <div class="mt-4 flex justify-end">
                            <button type="button" class="btn btn-secondary" @click="openStep = {{ $number + 1 }}">Next step <x-icon name="chevron" class="h-4 w-4" /></button>
                        </div>
                    @endif
                    @if ($pickModel)
                        <x-slot:summary><span x-text="{{ $pickModel }}.length + ' selected'"></span></x-slot:summary>
                    @endif
                </x-wizard-step>
            @endforeach

            <div class="sticky bottom-4 z-10 mt-6 flex items-center justify-between gap-3 rounded-3xl bg-white/90 p-3 pl-5 shadow-xl ring-1 ring-slate-200 backdrop-blur">
                <p class="text-sm text-slate-500">Changes are kept for this browser session.</p>
                <div class="flex gap-2">
                    <a href="{{ route('system.widgets.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">Save widget</button>
                </div>
            </div>
        </form>
    </div>
@endsection
