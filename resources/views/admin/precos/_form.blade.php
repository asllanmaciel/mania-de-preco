@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group-full">
        <label for="produto_id">Produto</label>
        <select id="produto_id" name="produto_id" required>
            <option value="">Selecionar produto</option>
            @foreach ($produtos as $produtoItem)
                <option value="{{ $produtoItem->id }}" @selected((string) old('produto_id', $preco->produto_id) === (string) $produtoItem->id)>
                    {{ $produtoItem->nome }}
                    @if ($produtoItem->categoria)
                        - {{ $produtoItem->categoria->nome }}
                    @endif
                    @if ($produtoItem->marca)
                        / {{ $produtoItem->marca->nome }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="loja_id">Loja</label>
        <select id="loja_id" name="loja_id" required>
            <option value="">Selecionar loja</option>
            @foreach ($lojasDaConta as $lojaItem)
                <option value="{{ $lojaItem->id }}" @selected((string) old('loja_id', $preco->loja_id) === (string) $lojaItem->id)>
                    {{ $lojaItem->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field-group">
        <label for="preco">Valor</label>
        <input id="preco" type="number" step="0.01" min="0" name="preco" value="{{ old('preco', $preco->preco) }}" required>
    </div>

    <div class="field-group">
        <label for="tipo_preco">Tipo de preco</label>
        <select id="tipo_preco" name="tipo_preco" required>
            @foreach (['dinheiro' => 'Dinheiro', 'pix' => 'Pix', 'boleto' => 'Boleto', 'cartao' => 'Cartao', 'parcelado' => 'Parcelado'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('tipo_preco', $preco->tipo_preco ?: 'dinheiro') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-group-full">
        <label for="url_produto">URL do produto</label>
        <input id="url_produto" type="url" name="url_produto" value="{{ old('url_produto', $preco->url_produto) }}">
        <small>Campo opcional para apontar para a pagina publica ou para a oferta da loja.</small>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.precos.index') }}">Voltar para precos</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
