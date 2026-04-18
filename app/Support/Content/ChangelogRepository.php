<?php

namespace App\Support\Content;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChangelogRepository
{
    public function all(): Collection
    {
        $directory = base_path('docs/changelogs');

        if (! File::isDirectory($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->reject(fn ($file) => $file->getFilename() === 'README.md')
            ->sortByDesc(fn ($file) => $file->getFilename())
            ->values()
            ->map(fn ($file) => $this->parseFile($file->getPathname()));
    }

    public function find(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    private function parseFile(string $path): array
    {
        $contents = File::get($path);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $title = $this->match('/^#\s+(.+)$/m', $contents) ?? Str::headline(Str::after($filename, '_'));

        return [
            'slug' => $filename,
            'filename' => $filename,
            'title' => trim($title),
            'tipo' => trim((string) $this->match('/\*\*tipo:\*\*\s*(.+)$/m', $contents)),
            'impacto' => trim((string) $this->match('/\*\*impacto:\*\*\s*(.+)$/m', $contents)),
            'modulo' => trim((string) $this->match('/\*\*modulo:\*\*\s*(.+)$/m', $contents)),
            'resumo' => trim((string) $this->section('Resumo executivo', $contents)),
            'entregas' => $this->bulletSection('Entregas realizadas', $contents),
            'estrategia' => trim((string) $this->section('Estrategia aplicada', $contents)),
            'resultado' => trim((string) $this->section('Resultado', $contents)),
            'data_label' => $this->dateLabel($filename),
        ];
    }

    private function match(string $pattern, string $contents): ?string
    {
        preg_match($pattern, $contents, $matches);

        return $matches[1] ?? null;
    }

    private function section(string $title, string $contents): ?string
    {
        $escaped = preg_quote($title, '/');
        preg_match("/## {$escaped}\R\R(.*?)(?=\R## |\z)/s", $contents, $matches);

        return isset($matches[1]) ? trim($matches[1]) : null;
    }

    private function bulletSection(string $title, string $contents): Collection
    {
        $section = $this->section($title, $contents);

        if (! $section) {
            return collect();
        }

        return collect(preg_split('/\R/', $section))
            ->map(fn ($line) => trim(Str::after($line, '- ')))
            ->filter()
            ->values();
    }

    private function dateLabel(string $filename): string
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})_(\d{2})(\d{2})(\d{2})/', $filename, $matches)) {
            return $filename;
        }

        return "{$matches[3]}/{$matches[2]}/{$matches[1]} {$matches[4]}:{$matches[5]}";
    }
}
