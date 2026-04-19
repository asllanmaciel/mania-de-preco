<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Access\ContaAccess;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function contas(): BelongsToMany
    {
        return $this->belongsToMany(Conta::class, 'conta_user')
            ->withPivot(['papel', 'ativo', 'ultimo_acesso_em'])
            ->withTimestamps();
    }

    public function alertasPreco()
    {
        return $this->hasMany(AlertaPreco::class);
    }

    public function avaliacoesLoja()
    {
        return $this->hasMany(AvaliacaoLoja::class);
    }

    public function movimentacoesFinanceiras()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return asset($this->avatar_path);
    }

    public function chamadosSuporte()
    {
        return $this->hasMany(ChamadoSuporte::class);
    }

    public function contasAtivas()
    {
        return $this->contas()->wherePivot('ativo', true);
    }

    public function ehSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function possuiAcessoAdmin(): bool
    {
        return $this->contasAtivas()->exists();
    }

    public function perfilPainel(): string
    {
        if ($this->ehSuperAdmin()) {
            return 'super-admin';
        }

        if ($this->possuiAcessoAdmin()) {
            return 'admin';
        }

        return 'cliente';
    }

    public function rotaInicialPainel(): string
    {
        return match ($this->perfilPainel()) {
            'super-admin' => route('super-admin.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('cliente.dashboard'),
        };
    }

    public function papelNaConta(?Conta $conta): ?string
    {
        if (! $conta) {
            return null;
        }

        $pivotConta = $this->contas->firstWhere('id', $conta->id);

        if ($pivotConta?->pivot) {
            return (string) $pivotConta->pivot->papel;
        }

        $contaVinculada = $this->contas()
            ->where('conta_id', $conta->id)
            ->first();

        return $contaVinculada?->pivot?->papel;
    }

    public function podeGerirEquipe(?Conta $conta): bool
    {
        return $this->podeNaConta($conta, 'equipe');
    }

    public function podeNaConta(?Conta $conta, string $capability): bool
    {
        return ContaAccess::can($this->papelNaConta($conta), $capability);
    }

    public function capacidadesNaConta(?Conta $conta): array
    {
        return ContaAccess::capabilitiesFor($this->papelNaConta($conta));
    }
}
