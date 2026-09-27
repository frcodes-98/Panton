@extends('layouts.system')

@section('title', 'Modules · Panton')

@section('heading', 'Module Configuration')

@section('main')
    <x-data-grid
        alpine="modulePage"
        title="Module configuration"
        storage-key="panton.modules"
        :rows="$modules"
        :columns="[
            ['key' => 'name', 'label' => 'Module name'],
            ['key' => 'description', 'label' => 'Module description'],
            ['key' => 'status', 'label' => 'Module status', 'format' => 'enabled'],
        ]"
        :search-fields="[
            ['key' => 'name', 'label' => 'Module name'],
            ['key' => 'description', 'label' => 'Description'],
        ]"
        sort-key="name"
    >
        <x-slot:toolbar>
            <button type="button" class="btn btn-primary" @click="openInstall()"><x-icon name="plus" class="h-4 w-4" /> Install module</button>
            <button type="button" class="btn btn-danger" @click="openUninstall()"><x-icon name="trash" class="h-4 w-4" /> Uninstall module</button>
            <button type="button" class="btn btn-secondary" @click="openEdit()"><x-icon name="pencil" class="h-4 w-4" /> Edit module</button>
            <button type="button" class="btn btn-secondary" @click="setStatus(true)">Enable module</button>
            <button type="button" class="btn btn-secondary" @click="setStatus(false)">Disable module</button>
        </x-slot:toolbar>
        <x-slot:dialogs>
            <x-modal name="install" title="Install module" submit="install()">
                <p class="text-sm text-slate-600">Browse module setup file (.xml)</p>
                <label class="flex cursor-pointer flex-col items-center gap-2 rounded-2xl border-2 border-dashed border-teal-200 bg-teal-50/50 px-4 py-8 text-center hover:border-teal-400">
                    <x-icon name="module" class="h-8 w-8 text-teal-600" />
                    <span class="text-sm font-medium text-teal-800" x-text="fileName || 'Choose a file'"></span>
                    <span class="text-xs text-slate-500">.xml setup file</span>
                    <input x-ref="moduleFile" type="file" accept=".xml,text/xml" class="sr-only" @change="fileName = $event.target.files[0]?.name || ''" />
                </label>
                <p x-show="error" x-text="error" class="text-sm text-rose-600"></p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="submit" class="btn btn-primary">Install</button>
                </x-slot:footer>
            </x-modal>
            <x-modal name="edit" title="Edit module properties" submit="saveEdit()" width="max-w-lg">
                <label class="label" for="module-name">Module name</label>
                <input id="module-name" x-model="draft.name" class="input" />
                <label class="label" for="module-description">Module description</label>
                <input id="module-description" x-model="draft.description" class="input" />
                <p x-show="error" x-text="error" class="text-sm text-rose-600"></p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </x-slot:footer>
            </x-modal>
            <x-modal name="uninstall" title="Uninstall module" :danger="true">
                <p class="text-sm text-slate-600">Uninstall <span class="font-medium text-slate-900" x-text="selected()?.name"></span> from this session? The file is not processed.</p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="button" class="btn bg-rose-600 text-white hover:bg-rose-700" @click="confirmUninstall()">Uninstall</button>
                </x-slot:footer>
            </x-modal>
        </x-slot:dialogs>
    </x-data-grid>
@endsection
