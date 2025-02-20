@extends('layouts.bt4.app')

@section('title', 'Agenda')

@section('content')

@include('prof_agenda.partials.nav')

<h3>Reservas</h3>

<form method="GET" class="form-horizontal" action="{{ route('prof_agenda.open_hour.index') }}">

    @livewire('prof-agenda.select-user-profesion',['profession_id' => $request->profession_id, 'user_id' => $request->user_id])

    <div class="row">
        <fieldset class="form-group col col-md-6">
            <label for="for_id_deis">Funcionario</label>
            @livewire('search-select-user', [
                'selected_id' => 'patient_id'
            ])
        </fieldset>

        <fieldset class="form-group col col-md-6">
            <label for="for_id_deis">Asistencia</label>
            <select class="form-control" name="assistance" id="">
                <option value="-1" @selected(!$request->assistance == -1)>Todos</option>
                <option value="1" @selected($request->assistance == 1)>Asiste</option>
                <option value="0" @selected($request->assistance == 0)>No asiste</option>
            </select>
        </fieldset>
    </div>

</form>

<hr>

<table class="table table-striped table-sm table-bordered">
	<thead>
		<tr>
            <th>ID</th>
            <th>F.Inicio</th>
			<th>F.Término</th>
            <th>Funcionario</th>
            <th>T.Actividad</th>
			<th>Paciente</th>
            <th>Asistencia</th>
		</tr>
	</thead>
	<tbody>
	@foreach($openHours as $openHour)
        @if($openHour->deleted_at) <tr class="table-warning">
        @elseif($openHour->assistance === 1) <tr class="table-success">
        @elseif($openHour->assistance === 0) <tr class="table-danger"> 
        @else <tr> @endif
            <td>{{$openHour->id}}</td>
            <td>{{$openHour->start_date->format('Y-m-d H:i')}}</td>
            <td>{{$openHour->end_date->format('Y-m-d H:i')}}</td>
            <td>@if($openHour->profesional){{$openHour->profesional->shortName}}@endif</td>
            <td>{{$openHour->activityType->name}}</td>
            <td>@if($openHour->patient){{$openHour->patient->shortName}}@endif</td>
            <td>
                @if($openHour->deleted_at) <a data-toggle="collapse" href="#hiddenrow{{$openHour->id}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                                                Eliminado
                                            </a>
                @elseif($openHour->assistance === 1) <i class="fa fa-check" aria-hidden="true"></i>
                @elseif($openHour->assistance === 0) <i class="fa fa-times" aria-hidden="true"></i>
                @endif
            </td>
		</tr>

        @if($openHour->deleted_at)
            <tr id="hiddenrow{{$openHour->id}}" class="collapse">
                <td colspan="7">@include('prof_agenda.partials.audit', ['audits' => $openHour->audits] )</td>
            </tr>
        @endif

	@endforeach
	</tbody>
</table>

@endsection