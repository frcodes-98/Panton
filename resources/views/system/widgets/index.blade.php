@extends('layouts.system')

@section('title', 'Widgets · Panton')

@section('heading', 'Widget Configuration')

@section('main')
    <x-data-grid
        alpine="widgetList"
        title="Widget listing"
        storage-key="panton.widgets"
        :rows="$widgets"
        :columns="[
            ['key' => 'name', 'label' => 'Widget name'],
            ['key' => 'description', 'label' => 'Widget description'],
            ['key' => 'status', 'label' => 'Widget status', 'format' => 'enabled'],
        ]"
        :search-fields="[
            ['key' => 'name', 'label' => 'Widget name'],
            ['key' => 'description', 'label' => 'Description'],
        ]"
        sort-key="name"
        :extra="['editUrl' => route('system.widgets.edit', ['widget' => '__ID__'])]"
    >
        <x-slot:toolbar>
            <a href="{{ route('system.widgets.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add widget</a>
            <button type="button" class="btn btn-secondary" @click="goEdit()"><x-icon name="pencil" class="h-4 w-4" /> Edit widget</button>
            <button type="button" class="btn btn-danger" @click="openDelete()"><x-icon name="trash" class="h-4 w-4" /> Delete widget</button>
            <button type="button" class="btn btn-secondary" @click="setStatus(false)">Disable widget</button>
            <button type="button" class="btn btn-secondary" @click="setStatus(true)">Enable widget</button>
        </x-slot:toolbar>
        <x-slot:dialogs>
            <x-modal name="delete" title="Delete widget" :danger="true">
                <p class="text-sm text-slate-600">Delete <span class="font-medium text-slate-900" x-text="selected()?.name"></span> from this session?</p>
                <x-slot:footer>
                    <button type="button" class="btn btn-secondary" @click="modal = null">Cancel</button>
                    <button type="button" class="btn bg-rose-600 text-white hover:bg-rose-700" @click="confirmDelete()">Delete</button>
                </x-slot:footer>
            </x-modal>
        </x-slot:dialogs>
    </x-data-grid>
@endsection
