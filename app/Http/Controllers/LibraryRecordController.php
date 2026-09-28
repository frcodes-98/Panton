<?php

namespace App\Http\Controllers;

use App\Data\DemoCatalog;
use App\Models\Area;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LibraryRecordController extends Controller
{
    public function storeArea(Request $request): JsonResponse
    {
        $data = $this->validateArea($request);

        $area = Area::query()->create([
            ...$data,
            'slug' => $this->uniqueSlug($data['name']),
            'sort_order' => (int) Area::query()->max('sort_order') + 1,
            'security' => ['Admin', 'Editor', 'View Only'],
        ]);

        return response()->json($area->toListing());
    }

    public function updateArea(Request $request, Area $area): JsonResponse
    {
        $data = $this->validateArea($request, $area);
        $data['slug'] = $this->uniqueSlug($data['name'], $area);
        $area->update($data);

        return response()->json($area->fresh()->toListing());
    }

    public function storeStaff(Request $request): JsonResponse
    {
        $staff = new Staff($this->staffAttributes($request));
        if ($request->filled('password')) {
            $staff->password = Hash::make($request->string('password')->toString());
        }
        $staff->save();
        $staff->areas()->sync($request->input('workspace_ids', []));

        return response()->json($staff->fresh()->load('areas')->toDirectory());
    }

    public function updateStaff(Request $request, Staff $staff): JsonResponse
    {
        $staff->fill($this->staffAttributes($request, $staff));
        if ($request->filled('password')) {
            $staff->password = Hash::make($request->string('password')->toString());
        }
        $staff->save();
        $staff->areas()->sync($request->input('workspace_ids', []));

        return response()->json($staff->fresh()->load('areas')->toDirectory());
    }

    private function validateArea(Request $request, ?Area $area = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('areas', 'name')->ignore($area)],
            'repository' => ['boolean'],
            'approver_ids' => ['array'],
            'control_ids' => ['array'],
        ]);

        return [
            'name' => $data['name'],
            'repository' => (bool) ($data['repository'] ?? false),
            'approver_ids' => $data['approver_ids'] ?? [],
            'control_ids' => $data['control_ids'] ?? [],
        ];
    }

    private function uniqueSlug(string $name, ?Area $ignore = null): string
    {
        $base = DemoCatalog::slug($name) ?: 'area';
        $slug = $base;
        $suffix = 2;

        while (Area::query()->where('slug', $slug)->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function staffAttributes(Request $request, ?Staff $staff = null): array
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', Rule::unique('staff', 'username')->ignore($staff)],
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('staff', 'email')->ignore($staff)],
            'department' => ['nullable', 'string', 'max:120'],
            'position' => ['nullable', 'string', 'max:120'],
            'contact' => ['nullable', 'string', 'max:40'],
            'fax' => ['nullable', 'string', 'max:40'],
            'branch' => ['nullable', 'string', 'max:80'],
            'employee_no' => ['nullable', 'string', 'max:40'],
            'employee_code' => ['nullable', 'string', 'max:40'],
            'user_type' => ['required', Rule::in(['Admin', 'Editor', 'View Only'])],
            'login_status' => ['required', Rule::in(['Enable', 'Disable'])],
            'suspend_login' => ['required', Rule::in(['Yes', 'No'])],
            'activation_date' => ['nullable', 'date'],
            'disable_date' => ['nullable', 'date'],
            'maximum_attempts' => ['required', 'integer', 'min:1'],
            'group_ids' => ['array'],
            'workspace_ids' => ['array'],
            'widget_ids' => ['array'],
            'delegate_ids' => ['array'],
            'password' => ['nullable', 'string', 'max:100'],
        ]);

        return [
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'department' => $data['department'] ?? '',
            'position' => $data['position'] ?? '',
            'contact' => $data['contact'] ?? '',
            'fax' => $data['fax'] ?? '',
            'branch' => $data['branch'] ?? '',
            'employee_no' => $data['employee_no'] ?? '',
            'employee_code' => $data['employee_code'] ?? '',
            'role' => $data['user_type'],
            'login_status' => $data['login_status'],
            'suspend_login' => $data['suspend_login'],
            'activation_date' => $data['activation_date'] ?? null,
            'disable_date' => $data['disable_date'] ?? null,
            'maximum_attempts' => $data['maximum_attempts'],
            'group_ids' => $data['group_ids'] ?? [],
            'widget_ids' => $data['widget_ids'] ?? [],
            'delegate_ids' => $data['delegate_ids'] ?? [],
        ];
    }
}
