<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('demo_user')) {
            return redirect()->route('landing');
        }

        return $next($request);
    }
}
