<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin;
use App\Models\Trainer;
use Illuminate\Http\Request;

class AdminTrainerGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if( $request->user() instanceof Admin || $request->user() instanceof Trainer)
        return $next($request);

        else
        return response()->json('Unauthorized',401);
    }
}
