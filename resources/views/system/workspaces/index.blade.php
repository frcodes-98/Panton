@extends('layouts.system')

@section('title', 'Workspaces · Panton')

@section('heading', 'Workspace Management')

@section('main')
    <x-data-grid
        alpine="workspaceList"
        title="Workspace configuration"
        storage-key="panton.workspaces"
        :rows="$workspaces"
        :columns="[
            ['key' => 'name', 'label' => 'Workspace name'],
        ]"
        :search-fields="[
            ['key' => 'name', 'label' => 'Workspace name'],
        ]"
        sort-key="name"
        :extra="['editUrl' => route('system.workspaces.edit', ['workspace' => '__ID__'])]"
    >
        <x-slot:toolbar>
            <a href="{{ route('system.workspaces.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" /> Create workspace
            </a>
            <button type="button" class="btn btn-secondary" @click="goEdit()">
                <x-icon name="pencil" class="h-4 w-4" /> Edit workspace
            </button>
        </x-slot:toolbar>
    </x-data-grid>
@endsection
