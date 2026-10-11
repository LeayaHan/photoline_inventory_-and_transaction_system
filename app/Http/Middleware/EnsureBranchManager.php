<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchManager
{
    public function handle(Request $request, Closure $next): Response
    {
        $manager = auth('web')->user();

        if (! $manager || strtolower((string) $manager->email) !== 'photolinemanager@gmail.com') {
            abort(403, 'Unauthorized. Branch Manager access only.');
        }

        return $next($request);
    }
}
