@extends('layouts.system')

@section('title', 'System configuration · Panton')

@section('heading', 'System Configuration')

@section('main')
    <x-data-grid
        alpine="settingsPage"
        title="System settings"
        storage-key="panton.settings"
        :rows="$settings"
        :columns="[
            ['key' => 'key', 'label' => 'Setting'],
            ['key' => 'value', 'label' => 'Value'],
        ]"
        :search-fields="[
            ['key' => 'key', 'label' => 'Setting'],
            ['key' => 'value', 'label' => 'Value'],
        ]"
        sort-key="key"
    >
        <x-slot:toolbar>
            <button type="button" class="btn btn-primary" @click="openCreate()"><x-icon name="plus" class="h-4 w-4" /> Add setting</button>
            <button type="button" class="btn btn-secondary" @click="openEdit()"><x-icon name="pencil" class="h-4 w-4" /> Edit setting</button>
            <button type="button" class="btn btn-danger" @click="openDelete()"><x-icon name="trash" class="h-4 w-4" /> Delete setting</button>
        </x-slot:toolbar>
        <x-slot:dialogs>
            <x-modal name="form" title-expr="editingId ? 'Edit setting' : 'Add setting'" submit="saveSetting()">
                <label class="label" for="setting-key">Setting</label>
                <input id="setting-key" x-model="draft.key" class="input font-mono uppercase" />
                <label class="label" for="setting-value">Value</label>
                <input id="setting-value" x-model="draft.value" class="input" />
                <p x-show="error" x-text="error" class="text-sm text-rose-600"></p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </x-slot:footer>
            </x-modal>
            <x-modal name="delete" title="Delete setting" :danger="true">
                <p class="text-sm text-slate-600">Delete <span class="font-medium text-slate-900" x-text="selected()?.key"></span>?</p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="button" class="btn bg-rose-600 text-white hover:bg-rose-700" @click="confirmDelete()">Delete</button>
                </x-slot:footer>
            </x-modal>
        </x-slot:dialogs>
    </x-data-grid>
@endsection
