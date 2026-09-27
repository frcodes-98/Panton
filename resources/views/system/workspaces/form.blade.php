@extends('layouts.system')

@section('title', ($mode === 'edit' ? 'Edit workspace' : 'Create workspace').' · Panton')

@section('heading', $mode === 'edit' ? 'Edit workspace' : 'Create workspace')

@section('main')
    @php
        $sections = [
            'approverIds' => ['Approvers', 'People who approve documents in this workspace', 'badge', 'from-emerald-400 to-teal-600'],
            'controlIds' => ['Document control', 'People who manage document versions', 'shield', 'from-sky-400 to-indigo-600'],
        ];
    @endphp

    <div x-data="workspaceForm(@js([
        'mode' => $mode,
        'workspaceId' => $workspaceId,
        'indexUrl' => route('system.workspaces.index'),
        'workspaces' => $workspaces,
        'users' => $users,
    ]))" x-cloak class="mx-auto max-w-4xl">
        <a href="{{ route('system.workspaces.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-teal-700 hover:text-teal-900">
            <x-icon name="chevron" class="h-4 w-4 rotate-180" /> Back to workspaces
        </a>
        <p x-show="error" class="mb-4 flex items-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            <span class="font-semibold">Check this:</span> <span x-text="error"></span>
        </p>

        <form x-show="!missing" class="space-y-4" @submit.prevent="save()">
            <section class="panel overflow-hidden">
                <div class="modal-head">
                    <h2 class="font-semibold">Workspace details</h2>
                    <p class="text-sm text-teal-50/85">Name the workspace and choose whether it is a document repository.</p>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-[1fr_auto] sm:items-end">
                    <div>
                        <label class="label" for="workspace-name">Name</label>
                        <input id="workspace-name" x-model="name" class="input" />
                    </div>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl px-4 py-2.5 ring-1 transition" :class="repository ? 'bg-teal-50 ring-teal-300' : 'bg-white ring-slate-200'">
                        <input type="checkbox" x-model="repository" class="h-4 w-4 accent-teal-600" />
                        <span class="text-sm font-medium text-slate-700">Repository</span>
                    </label>
                </div>
            </section>

            @foreach ($sections as $list => [$title, $hint, $icon, $color])
                <section class="panel p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-md {{ $color }}">
                                <x-icon :name="$icon" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="font-semibold text-slate-900">{{ $title }}</h2>
                                <p class="text-sm text-slate-500">{{ $hint }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" @click="userQuery = ''; modal = '{{ $list }}'">
                            <x-icon name="plus" class="h-4 w-4" /> Browse users
                        </button>
                    </div>
                    <div class="mt-4 flex min-h-12 flex-wrap items-center gap-2 rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 p-3">
                        <template x-for="person in chosen('{{ $list }}')" :key="person.id">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white py-1 pl-1 pr-2 text-sm shadow-sm ring-1 ring-slate-200">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-teal-400 to-cyan-600 text-[11px] font-bold text-white" x-text="person.full_name.charAt(0)"></span>
                                <span x-text="person.full_name"></span>
                                <button type="button" class="text-slate-400 hover:text-rose-600" @click="togglePerson('{{ $list }}', person.id)" aria-label="Remove">×</button>
                            </span>
                        </template>
                        <p x-show="chosen('{{ $list }}').length === 0" class="px-1 text-sm text-slate-400">No users selected yet.</p>
                    </div>
                </section>
            @endforeach

            <div class="sticky bottom-4 z-10 flex items-center justify-between gap-3 rounded-3xl bg-white/90 p-3 pl-5 shadow-xl ring-1 ring-slate-200 backdrop-blur">
                <p class="text-sm text-slate-500">Changes are kept for this browser session.</p>
                <div class="flex gap-2">
                    <a href="{{ route('system.workspaces.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">{{ $mode === 'edit' ? 'Save' : 'Create' }}</button>
                </div>
            </div>
        </form>

        <div x-show="modal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="modal = null">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" @click="modal = null"></div>
            <div class="modal-card flex max-h-[85vh] max-w-lg flex-col">
                <div class="modal-head flex items-center justify-between">
                    <h2 class="text-lg font-semibold" x-text="modal === 'approverIds' ? 'Add approvers' : 'Add document control'"></h2>
                    <button type="button" class="text-2xl leading-none text-white/80 hover:text-white" @click="modal = null" aria-label="Close">×</button>
                </div>
                <div class="border-b border-slate-100 p-4">
                    <label class="relative block">
                        <span class="sr-only">Search for users</span>
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input x-model="userQuery" type="search" placeholder="Search for users" class="input pl-9" />
                    </label>
                </div>
                <ul class="flex-1 divide-y divide-slate-100 overflow-y-auto">
                    <template x-for="user in filteredUsers()" :key="user.id">
                        <li>
                            <button type="button" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm hover:bg-teal-50/60" @click="togglePerson(modal, user.id)">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-teal-400 to-cyan-600 text-xs font-bold text-white" x-text="user.full_name.charAt(0)"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-medium text-slate-900" x-text="user.full_name"></span>
                                    <span class="text-slate-500" x-text="user.username + ' · ' + user.department"></span>
                                </span>
                                <span class="pill" :class="has(modal, user.id) ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-500'" x-text="has(modal, user.id) ? 'Added' : 'Add'"></span>
                            </button>
                        </li>
                    </template>
                    <li x-show="filteredUsers().length === 0" class="px-5 py-8 text-center text-sm text-slate-500">No users match that search.</li>
                </ul>
                <div class="flex justify-end border-t border-slate-100 bg-slate-50 px-6 py-4">
                    <button type="button" class="btn btn-primary" @click="modal = null">Done</button>
                </div>
            </div>
        </div>
    </div>
@endsection
