<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompleteSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (
            is_null($user->personalBio) ||
            !$user->educationalBios()->exists() ||
            is_null($user->professionalBio)
        ) {
            return redirect('/alumni/setup');
        }

        return $next($request);
    }
}
