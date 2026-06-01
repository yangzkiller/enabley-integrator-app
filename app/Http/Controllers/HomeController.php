<?php

namespace App\Http\Controllers;

use App\Services\EnableyService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        $enabley = new EnableyService();
        $group   = $enabley->getGroup(config('services.enabley.group_id'));

        $total      = DB::table('colaboradores')->count();
        $synced     = DB::table('colaboradores')->whereNotNull('enabley_identifier')->count();
        $pending    = $total - $synced;

        return Inertia::render('Home', [
            'group'   => $group ? [
                'name'       => $group->name,
                'identifier' => $group->identifier,
                'type'       => $group->type,
            ] : null,
            'stats' => [
                'total'   => $total,
                'synced'  => $synced,
                'pending' => $pending,
            ],
        ]);
    }
}