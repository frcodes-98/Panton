@extends('layouts.system')

@section('title', 'Areas · Panton')

@section('heading', 'Areas')

@section('main')
    <x-data-grid
        alpine="workspaceList"
        title="Area listing"
        :rows="$workspaces"
        :columns="[
            ['key' => 'name', 'label' => 'Area name'],
        ]"
        :search-fields="[
            ['key' => 'name', 'label' => 'Area name'],
        ]"
        sort-key="name"
        :extra="['editUrl' => route('system.workspaces.edit', ['workspace' => '__ID__'])]"
    >
        <x-slot:toolbar>
            <a href="{{ route('system.workspaces.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />                 Add area
            </a>
            <button type="button" class="btn btn-secondary" @click="goEdit()">
                <x-icon name="pencil" class="h-4 w-4" /> Edit area
            </button>
        </x-slot:toolbar>
    </x-data-grid>
@endsection
