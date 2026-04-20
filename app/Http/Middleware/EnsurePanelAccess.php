<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePanelAccess
{
    public function handle(Request $request, Closure $next, string $panel): Response
    {
        $user = $request->user();

        abort_unless($user, 403);

        $allowed = match ($panel) {
            'super-admin' => $user->ehSuperAdmin(),
            'admin' => $user->possuiAcessoAdmin(),
            'cliente' => true,
            default => false,
        };

        if (! $allowed && $request->isMethod('GET')) {
            return redirect()->to($user->rotaInicialPainel());
        }

        abort_unless($allowed, 403);

        return $next($request);
    }
}
