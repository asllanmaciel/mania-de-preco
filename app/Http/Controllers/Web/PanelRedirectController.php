<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PanelRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->to($request->user()->rotaInicialPainel());
    }
}
