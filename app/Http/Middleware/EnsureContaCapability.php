<?php

namespace App\Http\Middleware;

use App\Support\Access\ContaAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContaCapability
{
    public function handle(Request $request, Closure $next, string $capability): Response
    {
        $user = $request->user();

        abort_unless($user, 403);

        $conta = $user->contas()->wherePivot('ativo', true)->first();

        abort_unless($conta, 403);

        $papel = (string) $conta->pivot->papel;

        abort_unless(ContaAccess::can($papel, $capability), 403);

        return $next($request);
    }
}
