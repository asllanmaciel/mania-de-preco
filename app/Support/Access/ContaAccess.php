<?php

namespace App\Support\Access;

class ContaAccess
{
    public const ROLES = [
        'owner' => 'Owner',
        'gestor' => 'Gestor',
        'financeiro' => 'Financeiro',
        'operacao' => 'Operacao',
        'catalogo' => 'Catalogo',
        'viewer' => 'Viewer',
    ];

    public const CAPABILITIES = [
        'gestao' => ['owner', 'gestor'],
        'equipe' => ['owner', 'gestor'],
        'financeiro' => ['owner', 'gestor', 'financeiro'],
        'lojas' => ['owner', 'gestor', 'operacao', 'catalogo'],
        'catalogo' => ['owner', 'gestor', 'operacao', 'catalogo'],
        'precos' => ['owner', 'gestor', 'operacao', 'catalogo'],
        'onboarding' => ['owner', 'gestor', 'operacao', 'catalogo', 'financeiro'],
        'leitura' => ['owner', 'gestor', 'operacao', 'catalogo', 'financeiro', 'viewer'],
    ];

    public static function roles(): array
    {
        return self::ROLES;
    }

    public static function can(?string $role, string $capability): bool
    {
        if (! $role) {
            return false;
        }

        return in_array($role, self::CAPABILITIES[$capability] ?? [], true);
    }

    public static function capabilitiesFor(?string $role): array
    {
        return collect(array_keys(self::CAPABILITIES))
            ->filter(fn (string $capability) => self::can($role, $capability))
            ->values()
            ->all();
    }
}
