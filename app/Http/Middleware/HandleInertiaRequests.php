<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'root';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            //
        ]);
    }
}