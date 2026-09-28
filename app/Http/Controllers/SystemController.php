<?php

namespace App\Http\Controllers;

use App\Data\DemoCatalog;
use App\Models\Area;
use App\Models\Staff;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function welcome(): View
    {
        return view('system.welcome', [
            'counts' => [
                'users' => Staff::query()->count(),
                'groups' => count(DemoCatalog::groups()),
                'workspaces' => Area::query()->count(),
                'modules' => count(DemoCatalog::modules()),
            ],
        ]);
    }

    public function users(): View
    {
        return view('system.users.index', [
            'users' => Staff::query()->with('areas')->orderBy('username')->get()->map->toDirectory()->all(),
        ]);
    }

    public function createUser(): View
    {
        return view('system.users.form', $this->userFormData('create'));
    }

    public function editUser(int $user): View
    {
        return view('system.users.form', $this->userFormData('edit', $user));
    }

    public function groups(): View
    {
        return view('system.groups', [
            'groups' => DemoCatalog::groups(),
        ]);
    }

    public function workspaces(): View
    {
        return view('system.workspaces.index', [
            'workspaces' => Area::query()->orderBy('sort_order')->orderBy('name')->get()->map->toListing()->all(),
        ]);
    }

    public function createWorkspace(): View
    {
        return view('system.workspaces.form', $this->workspaceFormData('create'));
    }

    public function editWorkspace(int $workspace): View
    {
        return view('system.workspaces.form', $this->workspaceFormData('edit', $workspace));
    }

    public function modules(): View
    {
        return view('system.modules', [
            'modules' => DemoCatalog::modules(),
        ]);
    }

    public function widgets(): View
    {
        return view('system.widgets.index', [
            'widgets' => DemoCatalog::widgets(),
        ]);
    }

    public function createWidget(): View
    {
        return view('system.widgets.form', $this->widgetFormData('create'));
    }

    public function editWidget(int $widget): View
    {
        return view('system.widgets.form', $this->widgetFormData('edit', $widget));
    }

    public function settings(): View
    {
        return view('system.settings', [
            'settings' => DemoCatalog::settings(),
        ]);
    }

    public function marquee(): View
    {
        return view('system.marquee', [
            'marquee' => DemoCatalog::marquee(),
        ]);
    }

    private function userFormData(string $mode, ?int $userId = null): array
    {
        return [
            'mode' => $mode,
            'userId' => $userId,
            'users' => Staff::query()->with('areas')->orderBy('username')->get()->map->toDirectory()->all(),
            'groups' => DemoCatalog::groups(),
            'workspaces' => Area::query()->orderBy('sort_order')->orderBy('name')->get()->map->toListing()->all(),
            'widgets' => DemoCatalog::widgets(),
        ];
    }

    private function workspaceFormData(string $mode, ?int $workspaceId = null): array
    {
        return [
            'mode' => $mode,
            'workspaceId' => $workspaceId,
            'workspaces' => Area::query()->orderBy('sort_order')->orderBy('name')->get()->map->toListing()->all(),
            'users' => Staff::query()->with('areas')->orderBy('username')->get()->map->toDirectory()->all(),
        ];
    }

    private function widgetFormData(string $mode, ?int $widgetId = null): array
    {
        return [
            'mode' => $mode,
            'widgetId' => $widgetId,
            'widgets' => DemoCatalog::widgets(),
            'modules' => DemoCatalog::modules(),
            'workspaces' => DemoCatalog::workspaces(),
            'users' => DemoCatalog::users(),
            'groups' => DemoCatalog::groups(),
        ];
    }
}
