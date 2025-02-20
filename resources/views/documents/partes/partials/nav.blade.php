<ul class="nav nav-tabs mb-3">
    @can('Partes: oficina')
    <li class="nav-item">
        <a class="nav-link {{ active('documents.partes.create') }}"
            href="{{ route('documents.partes.create') }}">
            <i class="fas fa-plus"></i> Nuevo Ingreso
        </a>
    </li>
    @endcan

    @canany(['Partes: oficina', 'Partes: director'])
    <li class="nav-item">
        <a class="nav-link {{ active('documents.partes.index') }}"
            href="{{ route('documents.partes.index') }}">
            <i class="fas fa-folder-open"></i> Bandeja de Entrada
        </a>
    </li>
    @endcan

    @canany(['Partes: oficina'])
    <li class="nav-item">
        <a class="nav-link {{ active('documents.partes.report-by-dates') }}"
            href="{{ route('documents.partes.report-by-dates') }}">
            <i class="fas fa-search"></i> Buscar por fecha
        </a>
    </li>
    @endcan


    @canany(['Partes: oficina'])
    <!-- <li class="nav-item">
        <a class="nav-link"
            href="{{ route('documents.add_number') }}">
            <i class="fas fa-certificate"></i> Numerar y distribuir
        </a>
    </li> -->
    <li class="nav-item">
        <a class="nav-link {{ active('documents.partes.numeration.inbox') }}"
            href="{{ route('documents.partes.numeration.inbox') }}">
            <i class="fas fa-certificate"></i> Numerar y distribuir
        </a>
    </li>
    @endcan

    @canany(['Partes: oficina'])
    <li class="nav-item">
        <a class="nav-link"
            href="{{ route('documents.partes.outbox') }}">
            <i class="fas fa-inbox"></i> Bandeja de Salida
        </a>
    </li>
    @endcan

    @canany(['Partes: director'])
    <li class="nav-item">
        <a class="nav-link"
            href="{{ route('requirements.createFromParte') }}">
            <i class="fas fa-hands"></i> Derivar Pendientes
        </a>
    </li>
    @endcan
    
</ul>
