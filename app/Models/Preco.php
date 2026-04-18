<?php

namespace App\Models;

use App\Services\Precos\AlertaPrecoEvaluator;
use App\Services\Precos\HistoricoPrecoRecorder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preco extends Model
{
    use HasFactory;

    protected array $historicoSnapshot = [];

    protected $fillable = [
        'produto_id', 'loja_id', 'preco', 'tipo_preco', 'url_produto'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::created(function (Preco $preco) {
            app(HistoricoPrecoRecorder::class)->registrarCriacao($preco);
            app(AlertaPrecoEvaluator::class)->avaliarProduto($preco->produto_id);
        });

        static::updating(function (Preco $preco) {
            $preco->capturarHistoricoSnapshot();
        });

        static::updated(function (Preco $preco) {
            if (! $preco->mudouParaHistorico()) {
                return;
            }

            app(HistoricoPrecoRecorder::class)->registrarAtualizacao($preco, $preco->historicoSnapshot());
            app(AlertaPrecoEvaluator::class)->avaliarProduto($preco->produto_id);
        });

        static::deleting(function (Preco $preco) {
            $preco->capturarHistoricoSnapshot();
        });

        static::deleted(function (Preco $preco) {
            app(HistoricoPrecoRecorder::class)->registrarRemocao($preco, $preco->historicoSnapshot());
            app(AlertaPrecoEvaluator::class)->avaliarProduto($preco->historicoSnapshot()['produto_id'] ?? $preco->produto_id);
        });
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function historico()
    {
        return $this->hasMany(HistoricoPreco::class);
    }

    public function capturarHistoricoSnapshot(): void
    {
        $this->historicoSnapshot = [
            'produto_id' => (int) $this->getOriginal('produto_id', $this->produto_id),
            'loja_id' => (int) $this->getOriginal('loja_id', $this->loja_id),
            'preco' => $this->getOriginal('preco', $this->preco),
            'tipo_preco' => (string) $this->getOriginal('tipo_preco', $this->tipo_preco),
            'url_produto' => $this->getOriginal('url_produto', $this->url_produto),
        ];
    }

    public function historicoSnapshot(): array
    {
        return $this->historicoSnapshot;
    }

    public function mudouParaHistorico(): bool
    {
        return $this->wasChanged([
            'produto_id',
            'loja_id',
            'preco',
            'tipo_preco',
            'url_produto',
        ]);
    }
}
