@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group">
        <label for="descricao">Descricao</label>
        <input id="descricao" type="text" name="descricao" value="{{ old('descricao', $titulo->descricao) }}" required>
    </div>

    <div class="field-group">
        <label for="cliente_nome">Cliente</label>
        <input id="cliente_nome" type="text" name="cliente_nome" value="{{ old('cliente_nome', $titulo->cliente_nome) }}">
    </div>

    <div class="field-group">
        <label for="loja_id">Loja vinculada</label>
        <select id="loja_id" name="loja_id">
            <option value="">Sem loja especifica</option>
            @foreach ($lojas as $loja)
                <option value="{{ $loja->id }}" @selected((string) old('loja_id', $titulo->loja_id) === (string) $loja->id)>{{ $loja->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="conta_financeira_id">Conta financeira da baixa</label>
        <select id="conta_financeira_id" name="conta_financeira_id">
            <option value="">Selecionar conta</option>
            @foreach ($contasFinanceiras as $contaFinanceira)
                <option value="{{ $contaFinanceira->id }}" @selected((string) old('conta_financeira_id', $titulo->conta_financeira_id) === (string) $contaFinanceira->id)>{{ $contaFinanceira->nome }}</option>
            @endforeach
        </select>
        <small>Obrigatoria quando o status for `recebida`, porque a baixa automatica gera lancamento e ajusta saldo.</small>
    </div>

    <div class="field-group">
        <label for="categoria_financeira_id">Categoria</label>
        <select id="categoria_financeira_id" name="categoria_financeira_id">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) old('categoria_financeira_id', $titulo->categoria_financeira_id) === (string) $categoria->id)>
                    {{ $categoria->nome }} - {{ $categoria->tipo }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="valor_total">Valor total</label>
        <input id="valor_total" type="number" step="0.01" min="0" name="valor_total" value="{{ old('valor_total', $titulo->valor_total) }}" required>
    </div>

    <div class="field-group">
        <label for="valor_recebido">Valor recebido</label>
        <input id="valor_recebido" type="number" step="0.01" min="0" name="valor_recebido" value="{{ old('valor_recebido', $titulo->valor_recebido) }}">
    </div>

    <div class="field-group">
        <label for="vencimento">Vencimento</label>
        <input id="vencimento" type="date" name="vencimento" value="{{ old('vencimento', $titulo->vencimento?->format('Y-m-d')) }}" required>
    </div>

    <div class="field-group">
        <label for="recebimento_previsto_em">Recebimento previsto</label>
        <input id="recebimento_previsto_em" type="date" name="recebimento_previsto_em" value="{{ old('recebimento_previsto_em', $titulo->recebimento_previsto_em?->format('Y-m-d')) }}">
    </div>

    <div class="field-group">
        <label for="recebido_em">Recebido em</label>
        <input id="recebido_em" type="datetime-local" name="recebido_em" value="{{ old('recebido_em', $titulo->recebido_em?->format('Y-m-d\\TH:i')) }}">
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (['aberta' => 'Aberta', 'parcial' => 'Parcial', 'recebida' => 'Recebida', 'vencida' => 'Vencida', 'cancelada' => 'Cancelada'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('status', $titulo->status ?: 'aberta') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group-full">
        <label for="observacoes">Observacoes</label>
        <textarea id="observacoes" name="observacoes">{{ old('observacoes', $titulo->observacoes) }}</textarea>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.financeiro.contas-receber.index') }}">Voltar para contas a receber</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
