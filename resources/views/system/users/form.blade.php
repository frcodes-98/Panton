@extends('layouts.system')

@section('title', ($mode === 'edit' ? 'Edit user' : 'Create user').' · Panton')

@section('heading', $mode === 'edit' ? 'Edit user' : 'Create new user')

@section('main')
    @php
        $steps = $mode === 'edit'
            ? [
                1 => ['Edit user setting', 'Account, access dates and personal details', false],
                2 => ['Edit group assigned', 'Groups this user belongs to', false],
                3 => ['Edit workspace assigned', 'Workspaces this user can open', false],
                4 => ['Edit widget assigned', 'Widgets shown to this user', false],
                5 => ['Edit delegated user assigned', 'People who can act on this user’s behalf', false],
            ]
            : [
                1 => ['Add user', 'Account, access dates and personal details', false],
                2 => ['Assign group(s)', 'Groups this user belongs to', true],
                3 => ['Assign workspace(s)', 'For basic users', false],
                4 => ['Assign widget(s)', 'Widgets shown to this user', true],
            ];
        $pickModels = [2 => 'groupPicks', 3 => 'workspacePicks', 4 => 'widgetPicks', 5 => 'delegatePicks'];
        $groupColumns = [
            ['key' => 'name', 'label' => 'Group name'],
            ['key' => 'total_users', 'label' => 'Total users'],
        ];
        $workspaceColumns = [
            ['key' => 'name', 'label' => 'Workspace'],
        ];
        $widgetColumns = [
            ['key' => 'name', 'label' => 'Widget name'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'status', 'label' => 'Status', 'format' => 'enabled'],
        ];
        $userColumns = [
            ['key' => 'username', 'label' => 'Username'],
            ['key' => 'full_name', 'label' => 'Full name'],
            ['key' => 'department', 'label' => 'Department'],
        ];
        $field = 'grid gap-1.5 sm:grid-cols-[10rem_1fr] sm:items-center';
    @endphp

    <div
        x-data="userForm(@js([
            'mode' => $mode,
            'userId' => $userId,
            'indexUrl' => route('system.users.index'),
            'users' => $users,
        ]))"
        x-cloak
        class="mx-auto max-w-5xl"
    >
        <a href="{{ route('system.users.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-teal-700 hover:text-teal-900">
            <x-icon name="chevron" class="h-4 w-4 rotate-180" /> Back to users
        </a>

        <div class="mb-5 flex items-center gap-2">
            @foreach ($steps as $number => $step)
                <button type="button" class="h-1.5 flex-1 rounded-full transition" :class="openStep >= {{ $number }} ? 'bg-gradient-to-r from-teal-500 to-cyan-600' : 'bg-slate-200'" @click="openStep = {{ $number }}" title="{{ $step[0] }}"></button>
            @endforeach
        </div>

        <p x-ref="error" x-show="error" class="mb-4 flex items-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            <span class="font-semibold">Check this:</span> <span x-text="error"></span>
        </p>
        <div x-show="missing" class="panel p-6">
            <a href="{{ route('system.users.index') }}" class="btn btn-primary">Back to users</a>
        </div>

        <form x-show="!missing" class="space-y-3" @submit.prevent="save()">
            @foreach ($steps as $number => [$title, $hint, $optional])
                <x-wizard-step :number="$number" :title="$title" :hint="$hint" :optional="$optional">
                    @if ($number === 1)
                        <div class="grid gap-5 lg:grid-cols-2">
                            <fieldset class="space-y-3 rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                                <legend class="flex items-center gap-2 text-sm font-semibold text-teal-800">
                                    <span class="h-2 w-2 rounded-full bg-teal-500"></span> User setting
                                </legend>
                                <div class="{{ $field }}"><label class="label mb-0" for="username">Username <span class="text-rose-500">*</span></label><input id="username" x-model="form.username" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="password">Password <span class="text-rose-500">*</span></label><input id="password" type="password" x-model="form.password" class="input" autocomplete="new-password" placeholder="{{ $mode === 'edit' ? 'Leave blank to keep' : '' }}" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="confirm_password">Confirm password <span class="text-rose-500">*</span></label><input id="confirm_password" type="password" x-model="form.confirm_password" class="input" autocomplete="new-password" /></div>
                                <div class="{{ $field }}"><p class="label mb-0">User type <span class="text-rose-500">*</span></p><x-choice model="form.user_type" :options="['Superuser', 'Basic', 'Viewer']" /></div>
                                <div class="{{ $field }}"><p class="label mb-0">Login status <span class="text-rose-500">*</span></p><x-choice model="form.login_status" :options="['Enable', 'Disable']" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="activation_date">Activation date <span class="text-rose-500">*</span></label><input id="activation_date" type="date" x-model="form.activation_date" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="disable_date">Disable date <span class="text-rose-500">*</span></label><input id="disable_date" type="date" x-model="form.disable_date" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="maximum_attempts">Maximum attempts <span class="text-rose-500">*</span></label><input id="maximum_attempts" type="number" min="1" x-model="form.maximum_attempts" class="input max-w-28" /></div>
                                <div class="{{ $field }}"><p class="label mb-0">Suspend login <span class="text-rose-500">*</span></p><x-choice model="form.suspend_login" :options="['Yes', 'No']" /></div>
                            </fieldset>
                            <fieldset class="space-y-3 rounded-2xl bg-white p-5 ring-1 ring-slate-200">
                                <legend class="flex items-center gap-2 text-sm font-semibold text-cyan-800">
                                    <span class="h-2 w-2 rounded-full bg-cyan-500"></span> Personal information
                                </legend>
                                <div class="{{ $field }}"><label class="label mb-0" for="full_name">Full name <span class="text-rose-500">*</span></label><input id="full_name" x-model="form.full_name" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="department">Department <span class="text-rose-500">*</span></label><input id="department" x-model="form.department" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="position">Position</label><input id="position" x-model="form.position" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="email">Email address <span class="text-rose-500">*</span></label><input id="email" type="email" x-model="form.email" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="contact">Contact no.</label><input id="contact" x-model="form.contact" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="fax">Fax no.</label><input id="fax" x-model="form.fax" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="branch">Branch</label><input id="branch" x-model="form.branch" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="employee_no">Employee no.</label><input id="employee_no" x-model="form.employee_no" class="input" /></div>
                                <div class="{{ $field }}"><label class="label mb-0" for="employee_code">Employee code</label><input id="employee_code" x-model="form.employee_code" class="input" /></div>
                            </fieldset>
                        </div>
                    @elseif ($number === 2)
                        <x-picker :items="$groups" :columns="$groupColumns" model="groupPicks" storage-key="panton.groups" title="Group listing" />
                    @elseif ($number === 3)
                        <x-picker :items="$workspaces" :columns="$workspaceColumns" model="workspacePicks" storage-key="panton.workspaces" title="Workspace list" />
                    @elseif ($number === 4)
                        <x-picker :items="$widgets" :columns="$widgetColumns" model="widgetPicks" storage-key="panton.widgets" title="Widget listing" />
                    @else
                        <x-picker :items="$users" :columns="$userColumns" model="delegatePicks" storage-key="panton.users" :exclude-id="$userId" title="Delegated users" />
                    @endif
                    @if ($number < count($steps))
                        <div class="mt-4 flex justify-end">
                            <button type="button" class="btn btn-secondary" @click="openStep = {{ $number + 1 }}">Next step <x-icon name="chevron" class="h-4 w-4" /></button>
                        </div>
                    @endif
                    @isset($pickModels[$number])
                        <x-slot:summary><span x-text="{{ $pickModels[$number] }}.length + ' selected'"></span></x-slot:summary>
                    @endisset
                </x-wizard-step>
            @endforeach

            <div class="sticky bottom-4 z-10 mt-6 flex items-center justify-between gap-3 rounded-3xl bg-white/90 p-3 pl-5 shadow-xl ring-1 ring-slate-200 backdrop-blur">
                <p class="text-sm text-slate-500">Changes are kept for this browser session.</p>
                <div class="flex gap-2">
                    <a href="{{ route('system.users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">Save user</button>
                </div>
            </div>
        </form>
    </div>
@endsection
