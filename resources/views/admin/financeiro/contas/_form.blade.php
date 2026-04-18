@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group">
        <label for="nome">Nome da conta</label>
        <input id="nome" type="text" name="nome" value="{{ old('nome', $contaFinanceira->nome) }}" required>
        <small>Exemplos: caixa principal, banco digital, carteira da loja.</small>
    </div>

    <div class="field-group">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach (['caixa' => 'Caixa', 'banco' => 'Banco', 'cartao' => 'Cartao', 'carteira_digital' => 'Carteira digital'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('tipo', $contaFinanceira->tipo ?: 'caixa') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="loja_id">Loja vinculada</label>
        <select id="loja_id" name="loja_id">
            <option value="">Sem loja especifica</option>
            @foreach ($lojas as $loja)
                <option value="{{ $loja->id }}" @selected((string) old('loja_id', $contaFinanceira->loja_id) === (string) $loja->id)>{{ $loja->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="instituicao">Instituicao</label>
        <input id="instituicao" type="text" name="instituicao" value="{{ old('instituicao', $contaFinanceira->instituicao) }}">
    </div>

    <div class="field-group">
        <label for="agencia">Agencia</label>
        <input id="agencia" type="text" name="agencia" value="{{ old('agencia', $contaFinanceira->agencia) }}">
    </div>

    <div class="field-group">
        <label for="numero">Numero</label>
        <input id="numero" type="text" name="numero" value="{{ old('numero', $contaFinanceira->numero) }}">
    </div>

    <div class="field-group">
        <label for="saldo_inicial">Saldo inicial</label>
        <input id="saldo_inicial" type="number" step="0.01" name="saldo_inicial" value="{{ old('saldo_inicial', $contaFinanceira->saldo_inicial) }}">
    </div>

    <div class="field-group">
        <label for="saldo_atual">Saldo atual</label>
        <input id="saldo_atual" type="number" step="0.01" name="saldo_atual" value="{{ old('saldo_atual', $contaFinanceira->saldo_atual) }}">
    </div>

    <div class="field-group-full">
        <label class="remember-toggle" for="ativa">
            <input id="ativa" type="checkbox" name="ativa" value="1" @checked(old('ativa', $contaFinanceira->exists ? $contaFinanceira->ativa : true))>
            <span>Conta financeira ativa</span>
        </label>
        <small>Contas ativas ficam disponiveis para novos lancamentos no painel.</small>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.financeiro.contas.index') }}">Voltar para contas</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
