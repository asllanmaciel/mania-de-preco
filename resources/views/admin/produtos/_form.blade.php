@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group">
        <label for="nome">Nome do produto</label>
        <input id="nome" type="text" name="nome" value="{{ old('nome', $produto->nome) }}" required>
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (['ativo' => 'Ativo', 'inativo' => 'Inativo'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('status', $produto->status ?: 'ativo') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="categoria_id">Categoria existente</label>
        <select id="categoria_id" name="categoria_id">
            <option value="">Selecionar categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $produto->categoria_id) === (string) $categoria->id)>{{ $categoria->nome }}</option>
            @endforeach
        </select>
        <small>Se preferir, deixe em branco e crie uma categoria nova logo abaixo.</small>
    </div>

    <div class="field-group">
        <label for="nova_categoria_nome">Nova categoria</label>
        <input id="nova_categoria_nome" type="text" name="nova_categoria_nome" value="{{ old('nova_categoria_nome') }}">
    </div>

    <div class="field-group">
        <label for="marca_id">Marca existente</label>
        <select id="marca_id" name="marca_id">
            <option value="">Selecionar marca</option>
            @foreach ($marcas as $marca)
                <option value="{{ $marca->id }}" @selected((string) old('marca_id', $produto->marca_id) === (string) $marca->id)>{{ $marca->nome }}</option>
            @endforeach
        </select>
        <small>Marca e opcional. Tambem pode ser criada no ato do cadastro.</small>
    </div>

    <div class="field-group">
        <label for="nova_marca_nome">Nova marca</label>
        <input id="nova_marca_nome" type="text" name="nova_marca_nome" value="{{ old('nova_marca_nome') }}">
    </div>

    <div class="field-group-full">
        <label for="imagem_principal">Imagem principal</label>
        <input id="imagem_principal" type="text" name="imagem_principal" value="{{ old('imagem_principal', $produto->imagem_principal) }}">
        <small>Pode ser uma URL ou referencia para a proxima fase de midia do sistema.</small>
    </div>

    <div class="field-group-full">
        <label for="descricao">Descricao</label>
        <textarea id="descricao" name="descricao">{{ old('descricao', $produto->descricao) }}</textarea>
    </div>

    <div class="field-group-full">
        <label for="especificacoes_texto">Especificacoes</label>
        <textarea id="especificacoes_texto" name="especificacoes_texto">{{ old('especificacoes_texto', $especificacoesTexto) }}</textarea>
        <small>Use uma linha por especificacao. O sistema transforma isso em lista estruturada.</small>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.produtos.index') }}">Voltar para produtos</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
