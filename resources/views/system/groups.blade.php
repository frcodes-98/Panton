@extends('layouts.system')

@section('title', 'Groups · Panton')

@section('heading', 'Group Management')

@section('main')
    <x-data-grid
        alpine="groupPage"
        title="Group listing"
        storage-key="panton.groups"
        :rows="$groups"
        :columns="[
            ['key' => 'name', 'label' => 'Group name'],
            ['key' => 'total_users', 'label' => 'Total users', 'align' => 'right'],
        ]"
        :search-fields="[
            ['key' => 'name', 'label' => 'Group name'],
        ]"
        sort-key="name"
    >
        <x-slot:toolbar>
            <button type="button" class="btn btn-primary" @click="openCreate()">
                <x-icon name="plus" class="h-4 w-4" /> Add group
            </button>
            <button type="button" class="btn btn-secondary" @click="openEdit()">
                <x-icon name="pencil" class="h-4 w-4" /> Edit group
            </button>
            <button type="button" class="btn btn-danger" @click="openDelete()">
                <x-icon name="trash" class="h-4 w-4" /> Delete group
            </button>
            <button type="button" class="btn btn-secondary" @click="listAll()">List group</button>
        </x-slot:toolbar>
        <x-slot:dialogs>
            <x-modal name="form" title-expr="editingId ? 'Edit group' : 'Add group'" submit="saveGroup()">
                <label class="label" for="group-name">Group name</label>
                <input id="group-name" x-model="draftName" class="input" />
                <p x-show="error" x-text="error" class="text-sm text-rose-600"></p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </x-slot:footer>
            </x-modal>
            <x-modal name="delete" title="Delete group" :danger="true">
                <p class="text-sm text-slate-600">Delete <span class="font-medium text-slate-900" x-text="selected()?.name"></span> from this session?</p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="button" class="btn bg-rose-600 text-white hover:bg-rose-700" @click="confirmDelete()">Delete</button>
                </x-slot:footer>
            </x-modal>
        </x-slot:dialogs>
    </x-data-grid>
@endsection
