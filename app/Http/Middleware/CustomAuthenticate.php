<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (! Auth::guard($guards)->check()) {
            throw new AuthenticationException(
                'Custom unauthenticated.',
                $guards,
                $this->redirectTo($request)
            );
        }

        return $next($request);
    }

    protected function redirectTo($request)
    {
        // Customize the response here
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
