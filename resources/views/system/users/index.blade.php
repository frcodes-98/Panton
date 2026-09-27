@extends('layouts.system')

@section('title', 'Users · Panton')

@section('heading', 'User Management')

@section('main')
    <x-data-grid
        alpine="userList"
        title="User listing"
        storage-key="panton.users"
        :rows="$users"
        :columns="[
            ['key' => 'username', 'label' => 'Username'],
            ['key' => 'full_name', 'label' => 'Full name'],
            ['key' => 'department', 'label' => 'Department'],
            ['key' => 'login_status', 'label' => 'Login status', 'format' => 'login'],
            ['key' => 'suspend_login', 'label' => 'Suspend login', 'format' => 'suspend'],
            ['key' => 'last_login', 'label' => 'Last login'],
            ['key' => 'last_logout', 'label' => 'Last logout'],
        ]"
        :search-fields="[
            ['key' => 'username', 'label' => 'Username'],
            ['key' => 'full_name', 'label' => 'Full name'],
            ['key' => 'department', 'label' => 'Department'],
        ]"
        sort-key="username"
        :extra="['editUrl' => route('system.users.edit', ['user' => '__ID__'])]"
    >
        <x-slot:toolbar>
            <a href="{{ route('system.users.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Add user
            </a>
            <button type="button" class="btn btn-secondary" @click="goEdit()">
                <x-icon name="pencil" class="h-4 w-4" />
                Edit user
            </button>
        </x-slot:toolbar>
    </x-data-grid>
@endsection
