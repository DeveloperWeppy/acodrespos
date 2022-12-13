<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pqrs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ValidateAccesPqr
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
       //dd($request->id);
        //return $next($request);
        return redirect('/pqrs-acceso/validacion');
    }
}
