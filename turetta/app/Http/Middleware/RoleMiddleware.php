<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Verifica se o usuário autenticado possui um dos roles permitidos.
     *
     * Uso na rota: ->middleware('role:1') ou ->middleware('role:1,2')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $allowedRoles = array_map('intval', $roles);

        if (! in_array($request->user()->role_id, $allowedRoles, true)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
