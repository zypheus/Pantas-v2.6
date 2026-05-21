<nav class="ebooks-subnav" aria-label="E-Resources">
    <a href="{{ route('ebooks.index') }}"
       class="ebooks-subnav__link {{ request()->routeIs('ebooks.index', 'ebooks.edit') ? 'active' : '' }}">
        Collection
    </a>
    <a href="{{ route('ebooks.create') }}"
       class="ebooks-subnav__link {{ request()->routeIs('ebooks.create') ? 'active' : '' }}">
        Add e-resource
    </a>
</nav>
