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
        <label for="fornecedor_nome">Fornecedor</label>
        <input id="fornecedor_nome" type="text" name="fornecedor_nome" value="{{ old('fornecedor_nome', $titulo->fornecedor_nome) }}">
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
        <label for="valor_pago">Valor pago</label>
        <input id="valor_pago" type="number" step="0.01" min="0" name="valor_pago" value="{{ old('valor_pago', $titulo->valor_pago) }}">
    </div>

    <div class="field-group">
        <label for="vencimento">Vencimento</label>
        <input id="vencimento" type="date" name="vencimento" value="{{ old('vencimento', $titulo->vencimento?->format('Y-m-d')) }}" required>
    </div>

    <div class="field-group">
        <label for="pagamento_previsto_em">Pagamento previsto</label>
        <input id="pagamento_previsto_em" type="date" name="pagamento_previsto_em" value="{{ old('pagamento_previsto_em', $titulo->pagamento_previsto_em?->format('Y-m-d')) }}">
    </div>

    <div class="field-group">
        <label for="pago_em">Pago em</label>
        <input id="pago_em" type="datetime-local" name="pago_em" value="{{ old('pago_em', $titulo->pago_em?->format('Y-m-d\\TH:i')) }}">
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (['aberta' => 'Aberta', 'parcial' => 'Parcial', 'paga' => 'Paga', 'vencida' => 'Vencida', 'cancelada' => 'Cancelada'] as $valor => $rotulo)
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
    <a class="button-secondary" href="{{ route('admin.financeiro.contas-pagar.index') }}">Voltar para contas a pagar</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
