@extends('layouts.admin')

@section('title', 'Configuracoes')
@section('heading', 'Configuracoes da conta')
@section('subheading', 'Centralize dados da empresa, identidade comercial e preferencias de operacao para deixar a conta pronta para cobranca, suporte e crescimento.')

@section('content')
    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="grid-3">
        <article class="metric-card card">
            <span class="metric-label">Identidade</span>
            <strong class="metric-value">{{ $contaConfiguracao->documento ? 'OK' : 'Pendente' }}</strong>
            <span class="metric-trend {{ $contaConfiguracao->documento ? '' : 'is-danger' }}">dados fiscais e comerciais</span>
        </article>

        <article class="metric-card card">
            <span class="metric-label">Contato</span>
            <strong class="metric-value">{{ $contaConfiguracao->email && $contaConfiguracao->telefone ? 'OK' : 'Parcial' }}</strong>
            <span class="metric-trend {{ $contaConfiguracao->email && $contaConfiguracao->telefone ? '' : 'is-danger' }}">suporte e relacionamento</span>
        </article>

        <article class="metric-card card">
            <span class="metric-label">Marca</span>
            <strong class="metric-value">{{ $contaConfiguracao->descricao_publica ? 'Ativa' : 'Basica' }}</strong>
            <span class="metric-trend">conteudo para vitrine e operacao</span>
        </article>
    </section>

    <form method="POST" action="{{ route('admin.configuracoes.update') }}" class="stack">
        @csrf
        @method('PUT')

        <section class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Dados da empresa</h2>
                        <p>Informacoes que sustentam cobranca, suporte, governanca e relacionamento com o cliente.</p>
                    </div>
                    <span class="pill">base da conta</span>
                </div>

                <div class="form-grid">
                    <div class="field-group">
                        <label for="nome_fantasia">Nome fantasia</label>
                        <input id="nome_fantasia" type="text" name="nome_fantasia" value="{{ old('nome_fantasia', $contaConfiguracao->nome_fantasia) }}" required>
                    </div>

                    <div class="field-group">
                        <label for="razao_social">Razao social</label>
                        <input id="razao_social" type="text" name="razao_social" value="{{ old('razao_social', $contaConfiguracao->razao_social) }}">
                    </div>

                    <div class="field-group">
                        <label for="documento">CNPJ ou documento</label>
                        <input id="documento" type="text" name="documento" value="{{ old('documento', $contaConfiguracao->documento) }}">
                    </div>

                    <div class="field-group">
                        <label for="segmento">Segmento</label>
                        <select id="segmento" name="segmento">
                            <option value="">Selecione</option>
                            @foreach ($segmentos as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('segmento', $contaConfiguracao->segmento) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="porte">Porte da operacao</label>
                        <select id="porte" name="porte">
                            <option value="">Selecione</option>
                            @foreach ($portes as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('porte', $contaConfiguracao->porte) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="timezone">Fuso horario</label>
                        <input id="timezone" type="text" name="timezone" value="{{ old('timezone', $contaConfiguracao->timezone ?: 'America/Sao_Paulo') }}">
                        <small>Ajuda a padronizar relatorios, alertas e rotinas futuras.</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Contato e localizacao</h2>
                        <p>Dados usados para atendimento, cobranca, relacionamento e contexto operacional da empresa.</p>
                    </div>
                    <span class="pill">relacionamento</span>
                </div>

                <div class="form-grid">
                    <div class="field-group">
                        <label for="email">E-mail principal</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $contaConfiguracao->email) }}">
                    </div>

                    <div class="field-group">
                        <label for="telefone">Telefone ou WhatsApp</label>
                        <input id="telefone" type="text" name="telefone" value="{{ old('telefone', $contaConfiguracao->telefone) }}">
                    </div>

                    <div class="field-group">
                        <label for="site">Site</label>
                        <input id="site" type="url" name="site" value="{{ old('site', $contaConfiguracao->site) }}">
                    </div>

                    <div class="field-group">
                        <label for="instagram">Instagram</label>
                        <input id="instagram" type="text" name="instagram" value="{{ old('instagram', $contaConfiguracao->instagram) }}">
                    </div>

                    <div class="field-group-full">
                        <label for="endereco">Endereco</label>
                        <input id="endereco" type="text" name="endereco" value="{{ old('endereco', $contaConfiguracao->endereco) }}">
                    </div>

                    <div class="field-group">
                        <label for="numero">Numero</label>
                        <input id="numero" type="text" name="numero" value="{{ old('numero', $contaConfiguracao->numero) }}">
                    </div>

                    <div class="field-group">
                        <label for="bairro">Bairro</label>
                        <input id="bairro" type="text" name="bairro" value="{{ old('bairro', $contaConfiguracao->bairro) }}">
                    </div>

                    <div class="field-group">
                        <label for="cidade">Cidade</label>
                        <input id="cidade" type="text" name="cidade" value="{{ old('cidade', $contaConfiguracao->cidade) }}">
                    </div>

                    <div class="field-group">
                        <label for="uf">UF</label>
                        <input id="uf" type="text" maxlength="2" name="uf" value="{{ old('uf', $contaConfiguracao->uf) }}">
                    </div>

                    <div class="field-group">
                        <label for="cep">CEP</label>
                        <input id="cep" type="text" name="cep" value="{{ old('cep', $contaConfiguracao->cep) }}">
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Marca e preferencias</h2>
                        <p>Camada de apresentacao e combinados operacionais para deixar a experiencia mais personalizada.</p>
                    </div>
                    <span class="pill">pronto para producao</span>
                </div>

                <div class="form-grid">
                    <div class="field-group">
                        <label for="logo">Logo</label>
                        <input id="logo" type="text" name="logo" value="{{ old('logo', $contaConfiguracao->logo) }}">
                        <small>Por enquanto pode ser URL ou caminho interno. Depois evoluimos para upload real.</small>
                    </div>

                    <div class="field-group">
                        <label for="cor_marca">Cor principal</label>
                        <input id="cor_marca" type="text" name="cor_marca" value="{{ old('cor_marca', $contaConfiguracao->cor_marca) }}" placeholder="#ff6b2c">
                    </div>

                    <div class="field-group-full">
                        <label for="descricao_publica">Descricao comercial</label>
                        <textarea id="descricao_publica" name="descricao_publica" maxlength="1200">{{ old('descricao_publica', $contaConfiguracao->descricao_publica) }}</textarea>
                        <small>Texto-base para suporte, apresentacao comercial e futuras paginas publicas da empresa.</small>
                    </div>

                    <div class="field-group">
                        <label for="canal_suporte">Canal preferido de suporte</label>
                        <select id="canal_suporte" name="canal_suporte">
                            @foreach (['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'telefone' => 'Telefone'] as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('canal_suporte', $preferencias['canal_suporte']) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="frequencia_relatorio">Frequencia de relatorio</label>
                        <select id="frequencia_relatorio" name="frequencia_relatorio">
                            @foreach (['diaria' => 'Diaria', 'semanal' => 'Semanal', 'mensal' => 'Mensal'] as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('frequencia_relatorio', $preferencias['frequencia_relatorio']) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group-full">
                        <label class="remember-toggle">
                            <input type="checkbox" name="receber_alertas_operacionais" value="1" @checked(old('receber_alertas_operacionais', $preferencias['receber_alertas_operacionais']))>
                            Receber alertas operacionais e sinais importantes da conta
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="button-secondary" href="{{ route('admin.dashboard') }}">Voltar ao dashboard</a>
                    <button class="button" type="submit">Salvar configuracoes</button>
                </div>
            </div>
        </section>
    </form>
@endsection
