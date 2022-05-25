<?php

namespace App\Http\Middleware;

use App\Models\Trainer;
use Closure;
use Illuminate\Http\Request;

class TrainerGuard
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
        if( $request->user() instanceof Trainer)
        return $next($request);

        else
        return response()->json('Unauthorized',401);

    }
}
