<nav class="subnav">
    <a class="subnav-link {{ request()->routeIs('admin.financeiro.index') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.index') }}">
        Visao geral
    </a>
    <a class="subnav-link {{ request()->routeIs('admin.financeiro.contas.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.contas.index') }}">
        Contas financeiras
    </a>
    <a class="subnav-link {{ request()->routeIs('admin.financeiro.lancamentos.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.lancamentos.index') }}">
        Lancamentos
    </a>
    <a class="subnav-link {{ request()->routeIs('admin.financeiro.contas-pagar.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.contas-pagar.index') }}">
        Contas a pagar
    </a>
    <a class="subnav-link {{ request()->routeIs('admin.financeiro.contas-receber.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.contas-receber.index') }}">
        Contas a receber
    </a>
</nav>
