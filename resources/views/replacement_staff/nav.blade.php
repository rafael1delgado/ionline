<ul class="nav nav-tabs mb-3 d-print-none">

    @can('Replacement Staff: list rrhh')
    <li class="nav-item">
        <a class="nav-link"
                      href="{{ route('replacement_staff.index') }}">
            <i class="fas fa-inbox"></i> Listado Staff
        </a>
    </li>
    @endcan

    @if(Auth::user()->hasPermissionTo('Replacement Staff: create request') ||
        Auth::user()->hasPermissionTo('Replacement Staff: technical evaluation') ||
        App\Rrhh\Authority::getAmIAuthorityFromOu(Carbon\Carbon::now(), 'manager', Auth::user()->id))
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                <i class="fas fa-inbox"></i> Solicitudes
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('replacement_staff.request.own_index') }}"><i class="fas fa-inbox"></i> Mis Solicitudes</a>

                <a class="dropdown-item disabled" href="{{ route('replacement_staff.request.ou_index') }}"><i class="fas fa-inbox"></i> Solicitudes de mi U.O.</a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('replacement_staff.request.create') }}"><i class="fas fa-plus"></i> Nueva Solicitud</a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('replacement_staff.request.to_sign') }}">
                    <i class="fas fa-check-circle"></i> Gestión de solicitudes
                    @if(App\Models\ReplacementStaff\RequestReplacementStaff::getPendingRequestToSign() > 0)
                        <span class="badge badge-secondary">{{ App\Models\ReplacementStaff\RequestReplacementStaff::getPendingRequestToSign() }} </span>
                    @endif
                </a>
           </div>
       </li>
    @endif

    @canany(['Replacement Staff: assign request', 'Replacement Staff: technical evaluation'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
            <i class="fas fa-users"></i> Unidad de Reclutamiento
        </a>

        <div class="dropdown-menu">
            @can('Replacement Staff: assign request')
            <a class="dropdown-item" href="{{ route('replacement_staff.request.index') }}"><i class="fas fa-user-tag"></i> Asignar Solicitud</a>
            @endcan
            @can('Replacement Staff: technical evaluation')
            <a class="dropdown-item" href="{{ route('replacement_staff.request.assign_index') }}"><i class="fas fa-inbox"></i> Reclutamiento: Evaluación Técnica</a>
            @endcan
        </div>
   </li>
   @endcan

    @canany(['Replacement Staff: manage'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
            <i class="fas fa-file"></i> Reportes
        </a>
        <div class="dropdown-menu">
            @can('Replacement Staff: manage')
            <a class="dropdown-item" href=""><i class="fas fa-file"></i> Evaluaciones por RR.HH.</a>
            @endcan
        </div>
   </li>
   @endcan

   @canany(['Replacement Staff: manage'])
   <li class="nav-item dropdown">
       <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
           <i class="fas fa-cog"></i> Configuración
       </a>
       <div class="dropdown-menu">
           @can('Replacement Staff: manage')
           <a class="dropdown-item" href="{{ route('replacement_staff.manage.profession.index') }}">Profesiones</a>
           <a class="dropdown-item" href="{{ route('replacement_staff.manage.profile.index') }}">Perfiles</a>
           @endcan
       </div>
  </li>
  @endcan
</ul>
