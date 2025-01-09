<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminScoped
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        if (count($roles) === 1 && $roles[0] === 'Superadmin') {
            $admin = $user->admin();

            if (!isset($admin)) {
                return redirect('/');
            }

            if ($admin->is_super) {
                return $next($request);
            }
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return redirect('/');
    }
}
