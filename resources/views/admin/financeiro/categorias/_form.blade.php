<div class="form-grid">
    <div>
        <label for="nome">Nome</label>
        <input id="nome" type="text" name="nome" value="{{ old('nome', $categoria->nome) }}" required>
        @error('nome')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug">Slug</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $categoria->slug) }}" placeholder="gerado automaticamente">
        @error('slug')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="receita" @selected(old('tipo', $categoria->tipo) === 'receita')>Receita</option>
            <option value="despesa" @selected(old('tipo', $categoria->tipo) === 'despesa')>Despesa</option>
            <option value="ambos" @selected(old('tipo', $categoria->tipo) === 'ambos')>Ambos</option>
        </select>
        @error('tipo')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="cor">Cor</label>
        <input id="cor" type="text" name="cor" value="{{ old('cor', $categoria->cor) }}" placeholder="#0f766e">
        @error('cor')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="icone">Icone</label>
        <input id="icone" type="text" name="icone" value="{{ old('icone', $categoria->icone) }}" placeholder="wallet">
        @error('icone')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div style="display: flex; align-items: end;">
        <label style="display: inline-flex; gap: 10px; align-items: center; margin: 0;">
            <input type="checkbox" name="ativa" value="1" @checked(old('ativa', $categoria->ativa ?? true))>
            Categoria ativa
        </label>
    </div>
</div>

<div>
    <label for="descricao">Descricao</label>
    <textarea id="descricao" name="descricao" rows="4" placeholder="Explique quando essa categoria deve ser usada.">{{ old('descricao', $categoria->descricao) }}</textarea>
    @error('descricao')
        <p class="error-text">{{ $message }}</p>
    @enderror
</div>

<div class="toolbar-actions">
    <button class="button" type="submit">{{ $submitLabel }}</button>
    <a class="button-secondary" href="{{ route('admin.financeiro.categorias.index') }}">Cancelar</a>
</div>
