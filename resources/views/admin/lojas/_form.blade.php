@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group">
        <label for="nome">Nome da loja</label>
        <input id="nome" type="text" name="nome" value="{{ old('nome', $loja->nome) }}" required>
        <small>Nome comercial que aparecera no painel e no comparador.</small>
    </div>

    <div class="field-group">
        <label for="cnpj">CNPJ</label>
        <input id="cnpj" type="text" name="cnpj" value="{{ old('cnpj', $loja->cnpj) }}">
        <small>Campo opcional para identificar a operacao da loja.</small>
    </div>

    <div class="field-group">
        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email', $loja->email) }}">
    </div>

    <div class="field-group">
        <label for="telefone">Telefone</label>
        <input id="telefone" type="text" name="telefone" value="{{ old('telefone', $loja->telefone) }}">
    </div>

    <div class="field-group">
        <label for="whatsapp">WhatsApp</label>
        <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp', $loja->whatsapp) }}">
    </div>

    <div class="field-group">
        <label for="site">Site</label>
        <input id="site" type="url" name="site" value="{{ old('site', $loja->site) }}">
    </div>

    <div class="field-group">
        <label for="instagram">Instagram</label>
        <input id="instagram" type="text" name="instagram" value="{{ old('instagram', $loja->instagram) }}">
    </div>

    <div class="field-group">
        <label for="facebook">Facebook</label>
        <input id="facebook" type="text" name="facebook" value="{{ old('facebook', $loja->facebook) }}">
    </div>

    <div class="field-group-full">
        <label for="endereco">Endereco</label>
        <input id="endereco" type="text" name="endereco" value="{{ old('endereco', $loja->endereco) }}">
    </div>

    <div class="field-group">
        <label for="numero">Numero</label>
        <input id="numero" type="text" name="numero" value="{{ old('numero', $loja->numero) }}">
    </div>

    <div class="field-group">
        <label for="bairro">Bairro</label>
        <input id="bairro" type="text" name="bairro" value="{{ old('bairro', $loja->bairro) }}">
    </div>

    <div class="field-group">
        <label for="cidade">Cidade</label>
        <input id="cidade" type="text" name="cidade" value="{{ old('cidade', $loja->cidade) }}">
    </div>

    <div class="field-group">
        <label for="uf">UF</label>
        <input id="uf" type="text" name="uf" maxlength="2" value="{{ old('uf', $loja->uf) }}">
    </div>

    <div class="field-group">
        <label for="cep">CEP</label>
        <input id="cep" type="text" name="cep" value="{{ old('cep', $loja->cep) }}">
    </div>

    <div class="field-group">
        <label for="tipo_loja">Tipo da loja</label>
        <select id="tipo_loja" name="tipo_loja" required>
            @foreach (['fisica' => 'Fisica', 'online' => 'Online', 'mista' => 'Mista'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('tipo_loja', $loja->tipo_loja ?: 'fisica') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach (['ativo' => 'Ativo', 'inativo' => 'Inativo'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('status', $loja->status ?: 'ativo') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="latitude">Latitude</label>
        <input id="latitude" type="number" step="0.00000001" name="latitude" value="{{ old('latitude', $loja->latitude) }}">
    </div>

    <div class="field-group">
        <label for="longitude">Longitude</label>
        <input id="longitude" type="number" step="0.00000001" name="longitude" value="{{ old('longitude', $loja->longitude) }}">
    </div>

    <div class="field-group-full">
        <label for="logo">Logo</label>
        <input id="logo" type="text" name="logo" value="{{ old('logo', $loja->logo) }}">
        <small>Pode ser URL externa ou caminho de arquivo, se voce evoluir isso depois.</small>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.lojas.index') }}">Voltar para lojas</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
