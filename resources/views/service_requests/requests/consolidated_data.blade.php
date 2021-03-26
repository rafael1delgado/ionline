@extends('layouts.app')

@section('title', 'Reporte COVID-19')

@section('content')

@include('service_requests.partials.nav')

	<h3 class="mb-3">Reporte consolidado</h3>

	<form method="GET" class="form-horizontal" action="{{ route('rrhh.service-request.report.consolidated_data') }}">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
				<span class="input-group-text">Rango de fechas (Inicio de contrato)</span>
			</div>
			<!-- <input type="date" class="form-control" id="for_dateFrom" name="dateFrom" value="2021-01-01" required >
			<input type="date" class="form-control" id="for_dateTo" name="dateTo" value="2021-01-31" required> -->
			<input type="text" value="Todos los datos" disabled>
			<div class="input-group-append">
					<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
			</div>
		</div>
	</form>

<hr>

</main><main class="py-4">

	<h4>Solicitudes Activas <b>({{count($serviceRequests)}})</b></h4><br>
	<!-- <a type="button"
		 class="btn btn-outline-success" onclick="fnExcelReport();">
		 Descargar
		 <i class="far fa-file-excel"></i>
	 </a><br> -->
	 <a class="btn btn-outline-success btn-sm mb-3" id="downloadLink" onclick="exportF(this)">Descargar en excel</a>

<iframe id="txtArea1" style="display:none"></iframe>
<table class="table table-sm table-bordered table-responsive small" id="table_id">
    <thead>
        <tr class="text-center">
            <th>ID</th>
            <th>N° CONTRATO</th>
            <th>MES PAGO</th>
            <th>COD. SIRH S.S.</th>
            <th>SERVICIO DE SALUD</th>
            <th>COD EST.</th>
            <th>ESTABLECIMIENTO</th>
            <th>RUT</th>
            <th>APELLIDOS Y NOMBRES</th>
            <th>NACIONALIDAD</th>
            <th>NOMBRE DEL PROGRAMA SIRH</th>
            <th>ESTRATEGIA DIGERA COVID</th>
            <th>EQUIPO RRHH</th>
            <th>HORAS SEMANALES CONTRATADAS</th>
            <th>UNIDAD</th>
            <th>ESTAMENTO</th>
            <th>FUNCIÓN</th>
            <th>MONTO BRUTO</th>
            <th>CONTRATO REGISTRADO EN SIRH</th>
            <th>RESOLUCIÓN TRAMITADA</th>
            <th>N° RESOLUCIÓN</th>
            <th>FECHA INICIO</th>
            <th>FECHA TÉRMINO</th>
            <th>BOLETA Nº</th>
            <th>TOTAL HORAS PAGADAS PERIODO</th>
            <th>TOTAL PAGADO</th>
            <th>FECHA PAGO</th>
						<th>T.CONTRATO</th>
						<th>ESTADO.SOLICITUD</th>
						<th>JORNADA TRABAJO</th>
        </tr>
    </thead>
    <tbody>
      @foreach($serviceRequests as $key => $serviceRequest)
				<!-- si tiene cumplimiento -->
			  @if($serviceRequest->fulfillments->count() > 0)
					@foreach($serviceRequest->fulfillments as $key2 => $fulfillment)
						@if($fulfillment->month != 1)
							<tr class="table-success">
								<td nowrap>{{$serviceRequest->id}}</td>
								<td nowrap>{{$serviceRequest->contract_number}}</td>
								<td nowrap>{{$fulfillment->MonthOfPayment()}}</td>
								<td nowrap>12</td>
								<td nowrap>SERVICIO DE SALUD DE IQUIQUE</td>
								<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->sirh_code}}@endif</td>
								<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->name}}@endif</td>
								<td nowrap>{{$serviceRequest->rut}}</td>
								<td nowrap>{{$serviceRequest->name}}</td>
								<td nowrap>{{$serviceRequest->nationality}}</td>
								<td nowrap>{{$serviceRequest->programm_name}}</td>
								<td nowrap>{{$serviceRequest->digera_strategy}}</td>
								<td nowrap>{{$serviceRequest->rrhh_team}}</td>
								<td nowrap>{{$serviceRequest->weekly_hours}}</td>
								<td nowrap>{{$serviceRequest->responsabilityCenter->name}}</td>
								<td nowrap>{{$serviceRequest->estate}}</td>
								<td nowrap>@if($serviceRequest->estate == "Administrativo")
														Apoyo Administrativo
													@else
														Apoyo Clínico
													@endif</td>
								<td nowrap>{{$serviceRequest->gross_amount}}</td>
								<td nowrap>@if($serviceRequest->sirh_contract_registration === 1) Sí
													 @elseif($serviceRequest->sirh_contract_registration === 0) No @endif</td>
								<td nowrap>@if($serviceRequest->resolution_number)Sí @else No @endif</td>
								<td nowrap>@if($serviceRequest->resolution_number){{$serviceRequest->resolution_number}}@else En trámite @endif</td>
								<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->start_date)->format('Y-m-d')}}</td>
								<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->end_date)->format('Y-m-d')}}</td>
								<td nowrap>{{$fulfillment->bill_number}}</td>
								<td nowrap>{{$fulfillment->total_hours_paid}}</td>
								<td nowrap>@if($fulfillment->total_paid){{$fulfillment->total_paid}}@else En proceso de pago @endif</td>
								<td nowrap>@if($fulfillment->payment_date){{$fulfillment->payment_date->format('Y-m-d')}}@endif</td>
								<td nowrap>{{$serviceRequest->program_contract_type}}</td>
								<td nowrap>
										@if($serviceRequest->SignatureFlows->where('status','===',0)->count() > 0) Rechazada
										@elseif($serviceRequest->SignatureFlows->whereNull('status')->count() > 0) Pendiente
										@else Finalizada @endif</td>
								<td nowrap>working_day_type</td>
							</tr>
						@else
							@if($fulfillment->bill_number != NULL || $fulfillment->total_hours_paid != NULL || $fulfillment->total_paid != NULL ||
						      $fulfillment->payment_date != NULL || $fulfillment->contable_month != NULL)
									<tr class="table-success">
										<td nowrap>{{$serviceRequest->id}}</td>
										<td nowrap>{{$serviceRequest->contract_number}}</td>
										<td nowrap>{{$fulfillment->MonthOfPayment()}}</td>
										<td nowrap>12</td>
										<td nowrap>SERVICIO DE SALUD DE IQUIQUE</td>
										<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->sirh_code}}@endif</td>
										<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->name}}@endif</td>
										<td nowrap>{{$serviceRequest->rut}}</td>
										<td nowrap>{{$serviceRequest->name}}</td>
										<td nowrap>{{$serviceRequest->nationality}}</td>
										<td nowrap>{{$serviceRequest->programm_name}}</td>
										<td nowrap>{{$serviceRequest->digera_strategy}}</td>
										<td nowrap>{{$serviceRequest->rrhh_team}}</td>
										<td nowrap>{{$serviceRequest->weekly_hours}}</td>
										<td nowrap>{{$serviceRequest->responsabilityCenter->name}}</td>
										<td nowrap>{{$serviceRequest->estate}}</td>
										<td nowrap>@if($serviceRequest->estate == "Administrativo")
																Apoyo Administrativo
															@else
																Apoyo Clínico
															@endif</td>
										<td nowrap>{{$serviceRequest->gross_amount}}</td>
										<td nowrap>@if($serviceRequest->sirh_contract_registration === 1) Sí
															 @elseif($serviceRequest->sirh_contract_registration === 0) No @endif</td>
										<td nowrap>@if($serviceRequest->resolution_number)Sí @else No @endif</td>
										<td nowrap>@if($serviceRequest->resolution_number){{$serviceRequest->resolution_number}}@else En trámite @endif</td>
										<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->start_date)->format('Y-m-d')}}</td>
										<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->end_date)->format('Y-m-d')}}</td>
										<td nowrap>{{$fulfillment->bill_number}}</td>
										<td nowrap>{{$fulfillment->total_hours_paid}}</td>
										<td nowrap>@if($fulfillment->total_paid){{$fulfillment->total_paid}}@else En proceso de pago @endif</td>
										<td nowrap>@if($fulfillment->payment_date){{$fulfillment->payment_date->format('Y-m-d')}}@endif</td>
										<td nowrap>{{$serviceRequest->program_contract_type}}</td>
										<td nowrap>
												@if($serviceRequest->SignatureFlows->where('status','===',0)->count() > 0) Rechazada
												@elseif($serviceRequest->SignatureFlows->whereNull('status')->count() > 0) Pendiente
												@else Finalizada @endif</td>
										<td nowrap>working_day_type</td>
									</tr>
							@else
								<tr class="table-success">
									<td nowrap>{{$serviceRequest->id}}</td>
									<td nowrap>{{$serviceRequest->contract_number}}</td>
									<td nowrap>@if($serviceRequest->total_paid) Enero @endif</td>
									<td nowrap>12</td>
									<td nowrap>SERVICIO DE SALUD DE IQUIQUE</td>
									<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->sirh_code}}@endif</td>
									<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->name}}@endif</td>
									<td nowrap>{{$serviceRequest->rut}}</td>
									<td nowrap>{{$serviceRequest->name}}</td>
									<td nowrap>{{$serviceRequest->nationality}}</td>
									<td nowrap>{{$serviceRequest->programm_name}}</td>
									<td nowrap>{{$serviceRequest->digera_strategy}}</td>
									<td nowrap>{{$serviceRequest->rrhh_team}}</td>
									<td nowrap>{{$serviceRequest->weekly_hours}}</td>
									<td nowrap>{{$serviceRequest->responsabilityCenter->name}}</td>
									<td nowrap>{{$serviceRequest->estate}}</td>
									<td nowrap>@if($serviceRequest->estate == "Administrativo")
															Apoyo Administrativo
														@else
															Apoyo Clínico
														@endif</td>
									<td nowrap>{{$serviceRequest->gross_amount}}</td>
									<td nowrap>@if($serviceRequest->sirh_contract_registration === 1) Sí
														 @elseif($serviceRequest->sirh_contract_registration === 0) No @endif</td>
									<td nowrap>@if($serviceRequest->resolution_number)Sí @else No @endif</td>
									<td nowrap>@if($serviceRequest->resolution_number){{$serviceRequest->resolution_number}}@else En trámite @endif</td>
									<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->start_date)->format('Y-m-d')}}</td>
									<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->end_date)->format('Y-m-d')}}</td>
									<td nowrap>{{$serviceRequest->bill_number}}</td>
									<td nowrap>{{$serviceRequest->total_hours_paid}}</td>
									<td nowrap>@if($serviceRequest->total_paid){{$serviceRequest->total_paid}}@else En proceso de pago @endif</td>
									<td nowrap>@if($serviceRequest->payment_date){{$serviceRequest->payment_date->format('Y-m-d')}}@endif</td>
									<td nowrap>{{$serviceRequest->program_contract_type}}</td>
									<td nowrap>
											@if($serviceRequest->SignatureFlows->where('status','===',0)->count() > 0) Rechazada
											@elseif($serviceRequest->SignatureFlows->whereNull('status')->count() > 0) Pendiente
											@else Finalizada @endif</td>
									<td nowrap>working_day_type</td>
								</tr>
							@endif
						@endif
					@endforeach
				<!-- si no tiene cumplimiento, desde hoja de ruta -->
				@else
					<tr>
						<td nowrap>{{$serviceRequest->id}}</td>
						<td nowrap>{{$serviceRequest->contract_number}}</td>
						<td nowrap>@if($serviceRequest->total_paid) Enero @endif</td>
						<td nowrap>12</td>
						<td nowrap>SERVICIO DE SALUD DE IQUIQUE</td>
						<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->sirh_code}}@endif</td>
						<td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->name}}@endif</td>
						<td nowrap>{{$serviceRequest->rut}}</td>
						<td nowrap>{{$serviceRequest->name}}</td>
						<td nowrap>{{$serviceRequest->nationality}}</td>
						<td nowrap>{{$serviceRequest->programm_name}}</td>
						<td nowrap>{{$serviceRequest->digera_strategy}}</td>
						<td nowrap>{{$serviceRequest->rrhh_team}}</td>
						<td nowrap>{{$serviceRequest->weekly_hours}}</td>
						<td nowrap>{{$serviceRequest->responsabilityCenter->name}}</td>
						<td nowrap>{{$serviceRequest->estate}}</td>
						<td nowrap>@if($serviceRequest->estate == "Administrativo")
												Apoyo Administrativo
											@else
												Apoyo Clínico
											@endif</td>
						<td nowrap>{{$serviceRequest->gross_amount}}</td>
						<td nowrap>@if($serviceRequest->sirh_contract_registration === 1) Sí
											 @elseif($serviceRequest->sirh_contract_registration === 0) No @endif</td>
						<td nowrap>@if($serviceRequest->resolution_number)Sí @else No @endif</td>
						<td nowrap>@if($serviceRequest->resolution_number){{$serviceRequest->resolution_number}}@else En trámite @endif</td>
						<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->start_date)->format('Y-m-d')}}</td>
						<td nowrap>{{\Carbon\Carbon::parse($serviceRequest->end_date)->format('Y-m-d')}}</td>
						<td nowrap>{{$serviceRequest->bill_number}}</td>
						<td nowrap>{{$serviceRequest->total_hours_paid}}</td>
						<td nowrap>@if($serviceRequest->total_paid){{$serviceRequest->total_paid}}@else En proceso de pago @endif</td>
						<td nowrap>@if($serviceRequest->payment_date){{$serviceRequest->payment_date->format('Y-m-d')}}@endif</td>
						<td nowrap>{{$serviceRequest->program_contract_type}}</td>
						<td nowrap>
								@if($serviceRequest->SignatureFlows->where('status','===',0)->count() > 0) Rechazada
								@elseif($serviceRequest->SignatureFlows->whereNull('status')->count() > 0) Pendiente
								@else Finalizada @endif</td>
						<td nowrap>working_day_type</td>
					</tr>
			  @endif
      @endforeach
    </tbody>
</table>

<h4>Solicitudes Rechazadas <b>({{count($serviceRequestsRejected)}})</b></h4>

<table class="table table-sm table-bordered table-responsive small" >
    <thead>
        <tr class="text-center">
            <th>ID</th>
            <th>N° CONTRATO</th>
            <th>MES PAGO</th>
            <th>COD. SIRH S.S.</th>
            <th>SERVICIO DE SALUD</th>
            <th>COD EST.</th>
            <th>ESTABLECIMIENTO</th>
            <th>RUT</th>
            <th>APELLIDOS Y NOMBRES</th>
            <th>NACIONALIDAD</th>
            <th>NOMBRE DEL PROGRAMA SIRH</th>
            <th>ESTRATEGIA DIGERA COVID</th>
            <th>EQUIPO RRHH</th>
            <th>HORAS SEMANALES CONTRATADAS</th>
            <th>UNIDAD</th>
            <th>ESTAMENTO</th>
            <th>FUNCIÓN</th>
            <th>MONTO BRUTO</th>
            <th>CONTRATO REGISTRADO EN SIRH</th>
            <th>RESOLUCIÓN TRAMITADA</th>
            <th>N° RESOLUCIÓN</th>
            <th>FECHA INICIO</th>
            <th>FECHA TÉRMINO</th>
            <th>BOLETA Nº</th>
            <th>TOTAL HORAS PAGADAS PERIODO</th>
            <th>TOTAL PAGADO</th>
            <th>FECHA PAGO</th>
						<th>T.CONTRATO</th>
						<th>ESTADO.SOLICITUD</th>
        </tr>
    </thead>
    <tbody>
      @foreach($serviceRequestsRejected as $key => $serviceRequest)
        <tr>
          <td nowrap>{{$serviceRequest->id}}</td>
          <td nowrap>{{$serviceRequest->contract_number}}</td>
          <td nowrap>{{$serviceRequest->MonthOfPayment()}}</td>
          <td nowrap>12</td>
          <td nowrap>SERVICIO DE SALUD DE IQUIQUE</td>
          <td nowrap>130</td>
          <td nowrap>@if($serviceRequest->establishment){{$serviceRequest->establishment->name}}@endif</td>
          <td nowrap>{{$serviceRequest->rut}}</td>
          <td nowrap>{{$serviceRequest->name}}</td>
          <td nowrap>{{$serviceRequest->nationality}}</td>
          <td nowrap>{{$serviceRequest->programm_name}}</td>
          <td nowrap>{{$serviceRequest->digera_strategy}}</td>
          <td nowrap>{{$serviceRequest->rrhh_team}}</td>
          <td nowrap>{{$serviceRequest->weekly_hours}}</td>
          <td nowrap>{{$serviceRequest->responsabilityCenter->name}}</td>
          <td nowrap>{{$serviceRequest->estate}}</td>
          <td nowrap>@if($serviceRequest->estate == "Administrativo")
                      Apoyo Administrativo
                    @else
                      Apoyo Clínico
                    @endif</td>
          <td nowrap>{{$serviceRequest->gross_amount}}</td>
          <td nowrap>@if($serviceRequest->sirh_contract_registration === 1) Sí
                     @elseif($serviceRequest->sirh_contract_registration === 0) No @endif</td>
          <td nowrap>@if($serviceRequest->resolution_number)Sí @else No @endif</td>
          <td nowrap>@if($serviceRequest->resolution_number){{$serviceRequest->resolution_number}}@else En trámite @endif</td>
          <td nowrap>{{\Carbon\Carbon::parse($serviceRequest->start_date)->format('Y-m-d')}}</td>
          <td nowrap>{{\Carbon\Carbon::parse($serviceRequest->end_date)->format('Y-m-d')}}</td>
          <td nowrap>{{$serviceRequest->bill_number}}</td>
          <td nowrap>{{$serviceRequest->total_hours_paid}}</td>
          <td nowrap>@if($serviceRequest->total_paid){{$serviceRequest->total_paid}}@else En proceso de pago @endif</td>
          <td nowrap>@if($serviceRequest->payment_date){{$serviceRequest->payment_date->format('Y-m-d')}}@endif</td>
					<td nowrap>{{$serviceRequest->program_contract_type}}</td>
					<td nowrap>
							@if($serviceRequest->SignatureFlows->where('status','===',0)->count() > 0) Rechazada
							@elseif($serviceRequest->SignatureFlows->whereNull('status')->count() > 0) Pendiente
							@else Finalizada @endif</td>
					<td nowrap>working_day_type</td>
        </tr>
      @endforeach
    </tbody>
</table>

@endsection

@section('custom_js_head')
<script type="text/javascript">
    function exportF(elem) {
        var table = document.getElementById("table_id");
        var html = table.outerHTML;
        var html_no_links = html.replace(/<a[^>]*>|<\/a>/g, ""); //remove if u want links in your table
        var url = 'data:application/vnd.ms-excel,' + escape(html_no_links); // Set your html table into url
        elem.setAttribute("href", url);
        elem.setAttribute("download", "reporte_consolidado.xls"); // Choose the file name
        return false;
    }
</script>
@endsection
