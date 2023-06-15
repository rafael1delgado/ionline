<ul class="nav nav-tabs mb-3 d-print-none">
    <li class="nav-item">
        <a class="nav-link {{ active('summary.index') }}" href="{{ route('summary.index') }}">
            <i class="fas fa-book"></i> Mis Sumarios
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ active('summary.events.index') }}" href="{{ route('summary.events.index') }}">
            <i class="fas fa-list-alt"></i> Eventos
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ active('summary.links.index') }}" href="{{ route('summary.links.index') }}">
            <i class="fas fa-link"></i> Vínculos
        </a>
    </li>
</ul>
