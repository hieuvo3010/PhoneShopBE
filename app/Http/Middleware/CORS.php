<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)
        ->headers->set('Access-Control-Allow-Origin' , '*')
        ->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
        ->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin, User-Agent,Content-Type');
        
    }
}
