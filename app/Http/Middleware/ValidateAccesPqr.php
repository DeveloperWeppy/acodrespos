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
        $url = str_replace($_SERVER['HTTP_HOST'],'',$_SERVER['REQUEST_URI']);
        $dato = explode("/", $url);
        $data = $dato[2];
        //dd($dato[2]);
        //return $next($request);
        return  redirect()->route('pqrs.validateaccespqr', $data);
    }
}
