@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <section class="error-box">
            {{ $errors->first() }}
        </section>
    @endif

    <section class="card">
        <div class="card-body">
            <form class="stack" method="POST" action="@yield('form_action')">
                @csrf
                @yield('form_method')

                <div class="form-grid">
                    <div class="field-group">
                        <label for="name">Nome</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $membro?->name) }}">
                        <small>Obrigatorio quando o e-mail ainda nao pertence a um usuario existente.</small>
                    </div>

                    <div class="field-group">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $membro?->email) }}" required>
                    </div>

                    <div class="field-group">
                        <label for="password">Senha</label>
                        <input id="password" type="password" name="password">
                        <small>Obrigatoria para novos usuarios. Em edicao, preencha apenas se quiser trocar a senha.</small>
                    </div>

                    <div class="field-group">
                        <label for="papel">Papel</label>
                        <select id="papel" name="papel" required>
                            @foreach ($papeisDisponiveis as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('papel', $membro?->pivot?->papel ?? 'operacao') === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="remember-toggle" for="ativo">
                    <input id="ativo" type="checkbox" name="ativo" value="1" @checked(old('ativo', $membro?->pivot?->ativo ?? true))>
                    <span>Manter este acesso ativo para entrar no painel da conta</span>
                </label>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>Owner</strong>
                        <span>Controle total da conta, equipe e configuracao principal.</span>
                    </div>
                    <div class="mini-card">
                        <strong>Gestor</strong>
                        <span>Opera a conta e pode organizar a equipe junto do owner.</span>
                    </div>
                    <div class="mini-card">
                        <strong>Financeiro</strong>
                        <span>Foco em caixa, titulos e operacao financeira da conta.</span>
                    </div>
                    <div class="mini-card">
                        <strong>Operacao</strong>
                        <span>Execucao do dia a dia sem assumir governanca completa.</span>
                    </div>
                    <div class="mini-card">
                        <strong>Catalogo</strong>
                        <span>Responsavel por produtos, imagens e estrategia de preco.</span>
                    </div>
                    <div class="mini-card">
                        <strong>Viewer</strong>
                        <span>Acesso mais leve para acompanhamento e leitura interna.</span>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="button-secondary" href="{{ route('admin.equipe.index') }}">Voltar para equipe</a>
                    <button class="button" type="submit">Salvar membro</button>
                </div>
            </form>
        </div>
    </section>
@endsection
