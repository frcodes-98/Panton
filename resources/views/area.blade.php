@extends('layouts.base')

@section('title', $tile['name'].' · Panton')

@section('body-class', 'min-h-screen bg-slate-100 text-slate-800 antialiased')

@section('content')
    <div
        x-data="areaExplorer(@js([
            'name' => $tile['name'],
            'slug' => $tile['slug'],
            'libraryUrl' => $libraryUrl,
            'folders' => $library['folders'],
            'files' => $library['files'],
            'history' => $library['history'],
            'areaSecurity' => $library['areaSecurity'],
        ]))"
        x-cloak
        class="flex min-h-screen flex-col"
    >
        <header class="flex items-center justify-between gap-4 bg-gradient-to-r from-teal-700 to-cyan-800 px-4 py-3 text-white sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('desktop') }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 hover:bg-white/25" title="Back to desktop">
                    <x-icon name="chevron" class="h-4 w-4 rotate-180" />
                </a>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-teal-100">Area</p>
                    <h1 class="truncate text-lg font-semibold" x-text="name"></h1>
                </div>
            </div>
            <p class="hidden text-sm text-teal-50/90 sm:block">This area starts empty. Add folders, then documents.</p>
        </header>

        <p x-show="notice" class="bg-teal-50 px-4 py-2 text-sm text-teal-800 sm:px-6" x-text="notice"></p>

        <div class="grid min-h-0 flex-1 lg:grid-cols-[280px_1fr]">
            <aside class="border-b border-slate-200 bg-white lg:border-b-0 lg:border-r">
                <div class="flex items-center justify-between px-4 py-3">
                    <h2 class="text-sm font-semibold text-slate-800">Explorer</h2>
                    <button type="button" class="btn btn-primary px-2.5 py-1.5 text-xs" @click="startFolder(selectedFolderId)">
                        <x-icon name="plus" class="h-3.5 w-3.5" />
                        <span x-text="selectedFolderId ? 'Subfolder' : 'Folder'"></span>
                    </button>
                </div>
                <div class="px-4 pb-3">
                    <label class="relative block">
                        <span class="sr-only">Find a folder</span>
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input x-model="folderQuery" type="search" placeholder="Find a folder" class="input py-2 pl-9 text-sm" />
                    </label>
                </div>
                <div class="max-h-80 overflow-y-auto px-2 pb-4 lg:max-h-[calc(100vh-11rem)]">
                    <button type="button" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm" :class="selectedFolderId === null ? 'bg-teal-50 font-medium text-teal-900' : 'hover:bg-slate-50'" @click="selectFolder(null)">
                        <x-icon name="folder" class="h-4 w-4 text-teal-700" />
                        <span x-text="name"></span>
                    </button>
                    <template x-for="row in treeRows()" :key="row.id">
                        <div class="mt-0.5 flex items-center" :style="`padding-left: ${row.depth * 14}px`">
                            <button type="button" class="flex h-7 w-7 items-center justify-center text-slate-400" @click="toggleFolder(row.id)" :aria-label="row.open ? 'Collapse' : 'Expand'">
                                <span class="transition" :class="row.open ? 'rotate-90' : ''">
                                    <x-icon name="chevron" class="h-3.5 w-3.5" />
                                </span>
                            </button>
                            <button type="button" class="flex min-w-0 flex-1 items-center gap-2 rounded-xl px-2 py-1.5 text-left text-sm" :class="selectedFolderId === row.id ? 'bg-teal-50 font-medium text-teal-900' : 'hover:bg-slate-50'" @click="selectFolder(row.id)">
                                <x-icon name="folder" class="h-4 w-4 shrink-0 text-amber-500" />
                                <span class="truncate" x-text="row.name"></span>
                            </button>
                        </div>
                    </template>
                    <p x-show="folders.length === 0" class="px-3 py-6 text-sm text-slate-500">No folders yet. Add one, then add a subfolder inside it.</p>
                </div>
            </aside>

            <section class="min-w-0 p-4 sm:p-6">
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" @click="startFile()"><x-icon name="plus" class="h-4 w-4" /> Add</button>
                    <button type="button" class="btn btn-secondary" @click="trash()"><x-icon name="trash" class="h-4 w-4" /> Trash</button>
                    <button type="button" class="btn btn-secondary" @click="cut()">Cut</button>
                    <button type="button" class="btn btn-secondary" @click="copy()">Copy</button>
                    <button type="button" class="btn btn-secondary" @click="paste()">Paste</button>
                    <button type="button" class="btn btn-secondary" @click="openVersion()">Version</button>
                    <button type="button" class="btn btn-secondary" @click="openSecurity()">Security</button>
                    <button type="button" class="btn btn-secondary" @click="modal = 'history'">History</button>
                    <button type="button" class="btn btn-secondary" @click="openWorkflow()">Start workflow</button>
                </div>

                <div class="mt-4 flex flex-col gap-3 rounded-2xl bg-white p-3 ring-1 ring-slate-200 sm:flex-row sm:items-center">
                    <label class="relative min-w-0 flex-1">
                        <span class="sr-only">Find by description</span>
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input x-model="fileQuery" type="search" placeholder="Find by description" class="input py-2 pl-9" />
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        You are viewing
                        <select x-model="viewing" class="input w-auto py-2">
                            <option value="current">Current</option>
                            <option value="all">All</option>
                        </select>
                        documents.
                    </label>
                </div>

                <p class="mt-3 text-sm text-slate-500">
                    <span class="font-medium text-slate-700" x-text="selectedFolderId ? path(selectedFolderId) : name"></span>
                </p>

                <div class="mt-3 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gradient-to-r from-teal-700 to-cyan-800 text-white">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Reference no.</th>
                                    <th class="px-4 py-3 font-medium">Description</th>
                                    <th class="px-4 py-3 font-medium">Rev</th>
                                    <th class="px-4 py-3 font-medium">Effective</th>
                                    <th class="px-4 py-3 font-medium">Workflow</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in visibleFiles()" :key="item.id">
                                    <tr class="cursor-pointer border-t border-slate-100" :class="selectedFileId === item.id ? 'bg-teal-50' : 'hover:bg-slate-50'" @click="selectedFileId = item.id">
                                        <td class="px-4 py-3 font-medium" x-text="item.reference"></td>
                                        <td class="px-4 py-3" x-text="item.description"></td>
                                        <td class="px-4 py-3" x-text="item.rev"></td>
                                        <td class="px-4 py-3" x-text="item.effective || '—'"></td>
                                        <td class="px-4 py-3" x-text="item.workflow"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p x-show="visibleFiles().length === 0" class="px-4 py-16 text-center text-sm text-slate-500">No files found.</p>
                </div>
            </section>
        </div>

        <x-modal name="folder" title-expr="draftParentId ? 'New subfolder' : 'New folder'" submit="saveFolder()">
            <p class="text-sm text-slate-500" x-text="draftParentId ? `Inside ${path(draftParentId)}` : 'This folder sits at the top of the area.'"></p>
            <label class="label" for="folder-name">Folder name</label>
            <input id="folder-name" x-model="draftName" class="input" />
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </x-slot:footer>
        </x-modal>

        <x-modal name="file" title="Add document" submit="saveFile()">
            <p class="text-sm text-slate-500" x-text="selectedFolderId ? `Goes in ${path(selectedFolderId)}` : 'Goes at the top of this area. Select a folder first if it belongs inside one.'"></p>
            <label class="label" for="file-ref">Reference no.</label>
            <input id="file-ref" x-model="draftFile.reference" class="input" />
            <label class="label" for="file-desc">Description</label>
            <input id="file-desc" x-model="draftFile.description" class="input" />
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label" for="file-rev">Rev</label>
                    <input id="file-rev" x-model="draftFile.rev" class="input" />
                </div>
                <div>
                    <label class="label" for="file-effective">Effective</label>
                    <input id="file-effective" type="date" x-model="draftFile.effective" class="input" />
                </div>
            </div>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </x-slot:footer>
        </x-modal>

        <x-modal name="version" title="New version" submit="saveVersion()">
            <template x-if="file(selectedFileId)">
                <p class="text-sm text-slate-500">Current revision <span class="font-medium text-slate-800" x-text="file(selectedFileId).rev"></span></p>
            </template>
            <label class="label" for="new-rev">Rev</label>
            <input id="new-rev" x-model="draftRev.rev" class="input" />
            <label class="label" for="new-effective">Effective</label>
            <input id="new-effective" type="date" x-model="draftRev.effective" class="input" />
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                <button type="submit" class="btn btn-primary">Save version</button>
            </x-slot:footer>
        </x-modal>

        <x-modal name="security" title="Security">
            <p class="text-sm text-slate-500" x-text="selectedFolderId ? `Who can open ${path(selectedFolderId)}` : 'Who can open this area'"></p>
            <div class="space-y-2">
                <template x-for="role in ['Admin', 'Editor', 'View Only']" :key="role">
                    <label class="flex items-center gap-3 rounded-xl px-3 py-2 ring-1 ring-slate-200">
                        <input type="checkbox" class="h-4 w-4 accent-teal-600" :checked="securityList().includes(role)" @change="toggleSecurity(role)" />
                        <span x-text="role"></span>
                    </label>
                </template>
            </div>
            <x-slot:footer>
                <button type="button" class="btn btn-primary" @click="modal = null">Done</button>
            </x-slot:footer>
        </x-modal>

        <x-modal name="history" title="History" width="max-w-lg">
            <ul class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
                <template x-for="entry in history" :key="entry.id">
                    <li class="py-3 text-sm">
                        <span class="block font-medium" x-text="entry.text"></span>
                        <span class="text-slate-500" x-text="entry.at"></span>
                    </li>
                </template>
            </ul>
            <p x-show="history.length === 0" class="text-sm text-slate-500">Nothing has been added in this area yet.</p>
            <x-slot:footer>
                <button type="button" class="btn btn-primary" @click="modal = null">Close</button>
            </x-slot:footer>
        </x-modal>

        <x-modal name="workflow" title="Start workflow" submit="saveWorkflow()">
            <label class="label" for="workflow-status">Status</label>
            <select id="workflow-status" x-model="draftWorkflow" class="input">
                <option>Draft</option>
                <option>In review</option>
                <option>Approved</option>
                <option>Obsolete</option>
            </select>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </x-slot:footer>
        </x-modal>
    </div>
@endsection
