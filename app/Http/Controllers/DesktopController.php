<?php

namespace App\Http\Controllers;

use App\Data\DemoCatalog;
use Illuminate\View\View;

class DesktopController extends Controller
{
    public function index(): View
    {
        return view('desktop', [
            'tiles' => DemoCatalog::desktop(),
            'notices' => DemoCatalog::obsoleteNotices(),
        ]);
    }

    public function placeholder(string $workspace): View
    {
        $tile = collect(DemoCatalog::desktop())->firstWhere('slug', $workspace);
        abort_unless($tile, 404);

        return view('placeholder', ['tile' => $tile]);
    }
}
