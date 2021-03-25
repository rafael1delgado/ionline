<ul class="nav nav-tabs mb-3">


    <li class="nav-item">
        <a class="nav-link {{ active('rrhh.service_requests.index') }}"
            href="{{ route('rrhh.service_requests.index') }}">
            <i class="fas fa-pencil-alt"></i> Solicitudes
            <span class="badge badge-secondary">{{ App\Models\ServiceRequests\ServiceRequest::getPendingRequests() }}</span>
        </a>
    </li>

    @canany(['Service Request: fulfillments'])
    <li class="nav-item">
        <a class="nav-link {{ active('rrhh.fulfillments.index') }}"
            href="{{ route('rrhh.fulfillments.index') }}">
            <i class="fas fa-clipboard-check"></i> Cumplimientos
        </a>
    </li>
    @endcan

    @canany(['Service Request: additional data'])
    <li class="nav-item">
        <a class="nav-link {{ active('rrhh.service_requests.aditional_data_list') }}"
            href="{{ route('rrhh.service_requests.aditional_data_list') }}">
            <i class="fas fa-file-alt"></i> Información adicional
        </a>
    </li>
    @endcan

    @canany(['Service Request: transfer requests'])
    <li class="nav-item">
        <a class="nav-link {{ active('rrhh.service_requests.transfer_requests') }}"
            href="{{ route('rrhh.service_requests.transfer_requests') }}">
            <i class="fas fa-sign-in-alt"></i> Transferencia de solicitudes
        </a>
    </li>
    @endcan

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ active('rrhh.service_requests.report.*') }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-archive"></i> Reportes
        </a>
        <div class="dropdown-menu">

            @can('Service Request: consolidated data')
            <a class="dropdown-item"
                href="{{ route('rrhh.service_requests.consolidated_data') }}">
                <i class="far fa-file-excel"></i> Consolidado
            </a>
            @endcan

            @can('Service Request: consolidated data')
            <a class="dropdown-item"
                href="{{ route('rrhh.service-request.export-sirh') }}">
                <i class="far fa-file"></i> Formato SIRH
            </a>
            @endcan

            @canany(['Service Request: pending requests'])
            <a class="dropdown-item {{ active('rrhh.service_requests.pending_requests') }}"
                href="{{ route('rrhh.service_requests.pending_requests') }}">
                <i class="fas fa-bomb"></i> Estado de firmas
            </a>
            @endcan

            @canany(['Service Request: pending requests'])
            <a class="dropdown-item {{ active('rrhh.service_requests.report.toPay') }}"
                href="{{ route('rrhh.service_requests.report.toPay') }}">
                <i class="fas fa-file-invoice-dollar"></i> Reporte para pagos
            </a>
            @endcan


            <!-- @canany(['Service Request: pending requests'])
            <a class="dropdown-item {{ active('rrhh.service_requests.report.withoutBankDetails') }}"
                href="{{ route('rrhh.service_requests.report.withoutBankDetails') }}">
                <i class="fas fa-piggy-bank"></i> Sin Cuentas Bancarias
            </a>
            @endcan -->

            @canany(['Service Request: pending requests'])
            <a class="dropdown-item {{ active('rrhh.service_requests.report.pending-resolutions') }}"
                href="{{ route('rrhh.service_requests.report.pending-resolutions') }}">
                <i class="fas fa-file-invoice-dollar"></i> Resoluciones pendientes
            </a>
            @endcan

            @canany(['Service Request: with resolution'])
                <a class="dropdown-item {{ active('rrhh.service_requests.report.withResolutionFile') }}"
                   href="{{ route('rrhh.service_requests.report.withResolutionFile') }}">
                    <i class="fas fa-file-invoice-dollar"></i> Solicitudes con resolución cargada
                </a>
            @endcan


            <!-- <div class="dropdown-divider"></div>

            <a class="dropdown-item" href="#">Temp</a> -->

        </div>
    </li>

</ul>
