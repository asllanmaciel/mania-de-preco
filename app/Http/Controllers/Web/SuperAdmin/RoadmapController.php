<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Support\Lancamento\LaunchPreflight;
use App\Support\Lancamento\LaunchRoadmap;
use Illuminate\View\View;

class RoadmapController extends Controller
{
    public function __invoke(LaunchRoadmap $roadmap, LaunchPreflight $preflight): View
    {
        return view('super-admin.roadmap', [
            'roadmap' => $roadmap->analisar(),
            'preflight' => $preflight->analisar(),
        ]);
    }
}
