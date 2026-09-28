<?php

namespace App\Http\Controllers;

use App\Data\DemoCatalog;
use App\Models\Area;
use Illuminate\View\View;

class DesktopController extends Controller
{
    public function index(): View
    {
        $icons = collect(DemoCatalog::desktop())->pluck('icon', 'name');
        $tiles = Area::query()->orderBy('sort_order')->orderBy('name')->get()->map(fn (Area $area) => [
            'name' => $area->name,
            'slug' => $area->slug,
            'icon' => $icons[$area->name] ?? $area->icon,
            'kind' => 'workspace',
        ])->all();
        $tiles[] = [
            'name' => 'System Control',
            'slug' => 'system-control',
            'icon' => 'wrench',
            'kind' => 'app',
        ];

        return view('desktop', [
            'tiles' => $tiles,
            'notices' => DemoCatalog::obsoleteNotices(),
        ]);
    }

    public function show(Area $area): View
    {
        return view('area', [
            'tile' => [
                'name' => $area->name,
                'slug' => $area->slug,
                'icon' => $area->icon,
                'kind' => 'workspace',
            ],
            'library' => $area->toLibrary(),
            'libraryUrl' => route('areas.library', $area),
        ]);
    }
}
