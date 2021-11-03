<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class UserAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $allow_user_routes = !empty(config('userroutes')['routes']) ? config('userroutes')['routes'] : ['logout'];
      
        if(empty(auth()->user()->is_admin) && !empty(auth()->user())){ //condition to be removed
            return $next($request);
        }else if (empty(auth()->user()->is_admin) && !empty(auth()->user()) && !in_array($request->path(), $allow_user_routes)) {
            return redirect()->route('dashboard');
        } else if ($request->path() == 'register') {
            return redirect()->route('login');
        }

        return $next($request);
    }

}
