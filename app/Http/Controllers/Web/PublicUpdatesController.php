<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\Content\ChangelogRepository;
use Illuminate\View\View;

class PublicUpdatesController extends Controller
{
    public function index(ChangelogRepository $changelogs): View
    {
        return view('novidades.index', [
            'entries' => $changelogs->all(),
            'latest' => $changelogs->all()->first(),
        ]);
    }

    public function show(string $slug, ChangelogRepository $changelogs): View
    {
        $entry = $changelogs->find($slug);
        abort_unless($entry, 404);

        return view('novidades.show', [
            'entry' => $entry,
            'entries' => $changelogs->all()->take(6),
        ]);
    }
}
