<?php

namespace App\Http\Middleware;

use Closure;

class AdminStaffAuthMiddleware
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
        $userId = session('user_id');
        if(! isset($userId)){
            return redirect()->route('admin.get_login')
                ->with('admin_error','Please login to access that area!');;
        }
        return $next($request);
    }
}
