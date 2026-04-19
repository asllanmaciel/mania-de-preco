<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChamadoSuporte extends Model
{
    use HasFactory;

    protected $table = 'chamados_suporte';

    protected $fillable = [
        'conta_id',
        'user_id',
        'protocolo',
        'nome',
        'email',
        'telefone',
        'empresa',
        'categoria',
        'prioridade',
        'status',
        'assunto',
        'mensagem',
        'origem_url',
        'observacao_interna',
        'respondido_em',
        'resolvido_em',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'respondido_em' => 'datetime',
            'resolvido_em' => 'datetime',
        ];
    }

    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusLabel(): string
    {
        return self::statusDisponiveis()[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function categoriaLabel(): string
    {
        return self::categoriasDisponiveis()[$this->categoria] ?? ucfirst((string) $this->categoria);
    }

    public function prioridadeLabel(): string
    {
        return self::prioridadesDisponiveis()[$this->prioridade] ?? ucfirst((string) $this->prioridade);
    }

    public static function categoriasDisponiveis(): array
    {
        return [
            'acesso' => 'Acesso e senha',
            'cobranca' => 'Planos e cobranca',
            'catalogo' => 'Produtos, lojas e precos',
            'financeiro' => 'Financeiro',
            'privacidade' => 'Dados e privacidade',
            'tecnico' => 'Problema tecnico',
            'comercial' => 'Comercial',
            'outros' => 'Outros assuntos',
        ];
    }

    public static function prioridadesDisponiveis(): array
    {
        return [
            'baixa' => 'Baixa',
            'normal' => 'Normal',
            'alta' => 'Alta',
            'critica' => 'Critica',
        ];
    }

    public static function statusDisponiveis(): array
    {
        return [
            'novo' => 'Novo',
            'em_analise' => 'Em analise',
            'aguardando_cliente' => 'Aguardando cliente',
            'respondido' => 'Respondido',
            'resolvido' => 'Resolvido',
            'fechado' => 'Fechado',
        ];
    }
}
