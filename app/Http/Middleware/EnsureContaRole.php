<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContaRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 403);

        $conta = $user->contas()->wherePivot('ativo', true)->first();

        abort_unless($conta, 403);

        $papel = (string) $conta->pivot->papel;

        abort_unless(in_array($papel, $roles, true), 403);

        return $next($request);
    }
}
