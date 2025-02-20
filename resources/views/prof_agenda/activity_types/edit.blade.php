@extends('layouts.bt4.app')

@section('content')

@include('prof_agenda.partials.nav')

<h3 class="mb-3">Editar tipo de actividad</h3>

<form method="POST" class="form-horizontal" action="{{ route('prof_agenda.activity_types.update',$activityType) }}">
@csrf
@method('PUT')

<div class="row">
    <fieldset class="form-group col col-md">
        <label for="for_name">Nombre tipo de actividad</label>
        <input type="text" class="form-control" name="name" value="{{$activityType->name}}" required>
    </fieldset>

    <fieldset class="form-group col col-md">
        <label for="for_start_date">Tipo</label>
        <select class="form-control" name="reservable" id="">
            <option value=""></option>
            <option value="1" @selected($activityType->reservable == 1)>Reservable</option>
            <option value="0" @selected($activityType->reservable == 0)>No reservable</option>
        </select>
    </fieldset>
</div>

<button type="submit" class="btn btn-primary">Guardar</button>

</form>

@endsection

@section('custom_css')

@endsection

@section('custom_js')

@endsection
