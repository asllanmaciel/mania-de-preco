@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group-full">
        <label for="descricao">Descricao</label>
        <input id="descricao" type="text" name="descricao" value="{{ old('descricao', $movimentacao->descricao) }}" required>
    </div>

    <div class="field-group">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach (['receita' => 'Receita', 'despesa' => 'Despesa', 'transferencia' => 'Transferencia'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('tipo', $movimentacao->tipo ?: 'despesa') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="origem">Origem</label>
        <select id="origem" name="origem" required>
            @foreach (['manual' => 'Manual', 'venda' => 'Venda', 'pagamento' => 'Pagamento', 'ajuste' => 'Ajuste'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('origem', $movimentacao->origem ?: 'manual') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="valor">Valor</label>
        <input id="valor" type="number" step="0.01" min="0" name="valor" value="{{ old('valor', $movimentacao->valor) }}" required>
    </div>

    <div class="field-group">
        <label for="data_movimentacao">Data da movimentacao</label>
        <input
            id="data_movimentacao"
            type="datetime-local"
            name="data_movimentacao"
            value="{{ old('data_movimentacao', $movimentacao->data_movimentacao ? $movimentacao->data_movimentacao->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i')) }}"
            required
        >
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (['prevista' => 'Prevista', 'realizada' => 'Realizada', 'cancelada' => 'Cancelada'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('status', $movimentacao->status ?: 'realizada') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="conta_financeira_id">Conta financeira</label>
        <select id="conta_financeira_id" name="conta_financeira_id" required>
            <option value="">Selecionar conta</option>
            @foreach ($contasFinanceiras as $item)
                <option value="{{ $item->id }}" @selected((string) old('conta_financeira_id', $movimentacao->conta_financeira_id) === (string) $item->id)>
                    {{ $item->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="categoria_financeira_id">Categoria financeira</label>
        <select id="categoria_financeira_id" name="categoria_financeira_id">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) old('categoria_financeira_id', $movimentacao->categoria_financeira_id) === (string) $categoria->id)>
                    {{ $categoria->nome }} - {{ $categoria->tipo }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="loja_id">Loja vinculada</label>
        <select id="loja_id" name="loja_id">
            <option value="">Sem loja especifica</option>
            @foreach ($lojas as $loja)
                <option value="{{ $loja->id }}" @selected((string) old('loja_id', $movimentacao->loja_id) === (string) $loja->id)>{{ $loja->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group-full">
        <label for="observacoes">Observacoes</label>
        <textarea id="observacoes" name="observacoes">{{ old('observacoes', $movimentacao->observacoes) }}</textarea>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.financeiro.lancamentos.index') }}">Voltar para lancamentos</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
