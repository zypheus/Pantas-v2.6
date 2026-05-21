<nav class="accounts-subnav" aria-label="User accounts">
    <a href="{{ route('users.create') }}"
       class="accounts-subnav__link {{ request()->routeIs('users.create') ? 'active' : '' }}">
        Create account
    </a>
    <a href="{{ route('users.index') }}"
       class="accounts-subnav__link {{ request()->routeIs('users.index', 'users.edit') ? 'active' : '' }}">
        View users
    </a>
</nav>
