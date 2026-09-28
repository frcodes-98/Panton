<?php

namespace Database\Seeders;

use App\Data\DemoCatalog;
use App\Models\Area;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class PantonSeeder extends Seeder
{
    public function run(): void
    {
        $areas = collect(DemoCatalog::workspaces())->map(function (array $workspace, int $index) {
            return Area::query()->create([
                'name' => $workspace['name'],
                'slug' => DemoCatalog::slug($workspace['name']),
                'icon' => 'document',
                'repository' => $workspace['repository'],
                'security' => ['Admin', 'Editor', 'View Only'],
                'approver_ids' => $workspace['approver_ids'],
                'control_ids' => $workspace['control_ids'],
                'sort_order' => $index + 1,
            ]);
        });

        foreach (DemoCatalog::users() as $user) {
            $staff = Staff::query()->create([
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'department' => $user['department'],
                'position' => $user['position'],
                'role' => $user['user_type'],
                'login_status' => $user['login_status'],
                'suspend_login' => $user['suspend_login'],
                'activation_date' => '2026-09-15',
                'disable_date' => '2027-09-15',
                'maximum_attempts' => 5,
                'last_login' => $user['last_login'],
                'last_logout' => $user['last_logout'],
                'branch' => 'Main',
                'employee_no' => $user['employee_no'] ?? null,
                'employee_code' => $user['employee_code'] ?? null,
                'group_ids' => $user['group_ids'],
                'widget_ids' => $user['widget_ids'],
                'delegate_ids' => $user['delegate_ids'],
            ]);

            $areaIds = $areas->whereIn('sort_order', $user['workspace_ids'])->pluck('id');
            $staff->areas()->sync($areaIds);
        }
    }
}
