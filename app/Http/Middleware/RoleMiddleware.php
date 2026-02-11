<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'peserta' => redirect()->route('peserta.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
