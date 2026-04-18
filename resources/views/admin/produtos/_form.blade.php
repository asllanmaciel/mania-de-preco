@if ($errors->any())
    <div class="error-box">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="form-grid">
    <div class="field-group-full">
        <label>Preview</label>
        <div style="display:flex; gap:18px; align-items:center; padding:16px; border:1px solid rgba(15, 23, 42, 0.08); border-radius:18px; background:rgba(248, 250, 252, 0.9);">
            <img
                id="imagemPreview"
                src="{{ $produto->imagem_url }}"
                alt="Preview da imagem do produto"
                style="width:120px; height:120px; object-fit:cover; border-radius:20px; border:1px solid rgba(15, 23, 42, 0.08); background:#fff;"
            >
            <div style="display:grid; gap:8px;">
                <strong style="font-size:1rem;">Imagem principal do produto</strong>
                <small>Use uma URL valida ou um caminho local publico, como <code>/images/demo/produtos/cafe-premium-500g.svg</code>. Se ficar vazio, o sistema gera um placeholder visual automaticamente.</small>
            </div>
        </div>
    </div>

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
        <small>Ela sera usada no painel, na vitrine publica, na pagina da loja e no comparativo do produto.</small>
    </div>

    <div class="field-group-full">
        <label for="imagem_upload">Upload de imagem</label>
        <input id="imagem_upload" type="file" name="imagem_upload" accept="image/png,image/jpeg,image/webp,image/svg+xml">
        <small>Se voce enviar um arquivo, ele tem prioridade sobre a URL manual.</small>
    </div>

    @if ($produto->exists && $produto->imagem_principal)
        <div class="field-group-full">
            <label class="remember-toggle" for="remover_imagem">
                <input id="remover_imagem" type="checkbox" name="remover_imagem" value="1" @checked(old('remover_imagem'))>
                Remover imagem atual e voltar para o placeholder automatico
            </label>
        </div>
    @endif

    <div class="field-group-full">
        <label for="descricao">Descricao</label>
        <textarea id="descricao" name="descricao">{{ old('descricao', $produto->descricao) }}</textarea>
    </div>

    <div class="field-group-full">
        <label for="especificacoes_texto">Especificacoes</label>
        <textarea id="especificacoes_texto" name="especificacoes_texto">{{ old('especificacoes_texto', $especificacoesTexto) }}</textarea>
        <small>Use uma linha por especificacao. O sistema transforma isso em lista estruturada.</small>
    </div>

    <div class="field-group-full">
        <label for="galeria_imagens_texto">Galeria complementar</label>
        <textarea id="galeria_imagens_texto" name="galeria_imagens_texto" style="min-height: 120px;">{{ old('galeria_imagens_texto', $galeriaImagensTexto) }}</textarea>
        <small>Use uma linha por imagem adicional. Essas imagens enriquecem a pagina publica do produto e ajudam na navegacao mobile.</small>
    </div>
</div>

<div class="form-actions">
    <a class="button-secondary" href="{{ route('admin.produtos.index') }}">Voltar para produtos</a>
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>

<script>
    (() => {
        const input = document.getElementById('imagem_principal');
        const preview = document.getElementById('imagemPreview');

        if (!input || !preview) {
            return;
        }

        const fallback = preview.getAttribute('src');
        const upload = document.getElementById('imagem_upload');

        const updatePreview = () => {
            const value = input.value.trim();
            preview.src = value !== '' ? value : fallback;
        };

        const updateUploadPreview = () => {
            const file = upload?.files?.[0];

            if (!file) {
                updatePreview();

                return;
            }

            const reader = new FileReader();
            reader.onload = event => {
                preview.src = event.target?.result || fallback;
            };
            reader.readAsDataURL(file);
        };

        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
        upload?.addEventListener('change', updateUploadPreview);
    })();
</script>
