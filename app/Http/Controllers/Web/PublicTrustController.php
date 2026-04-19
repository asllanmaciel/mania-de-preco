<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PublicTrustController extends Controller
{
    public function termos(): View
    {
        return view('institucional.termos', $this->dadosBase());
    }

    public function privacidade(): View
    {
        return view('institucional.privacidade', $this->dadosBase());
    }

    public function suporte(): View
    {
        return view('institucional.suporte', $this->dadosBase());
    }

    private function dadosBase(): array
    {
        return [
            'ultimaAtualizacao' => '19/04/2026',
            'emailSuporte' => config('mail.from.address', 'suporte@maniadepreco.com.br'),
        ];
    }
}
