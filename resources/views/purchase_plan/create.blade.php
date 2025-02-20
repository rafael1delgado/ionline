@extends('layouts.bt5.app')

@section('title', 'Plan de Compras')

@section('content')

@include('purchase_plan.partials.nav')

<div class="row">
    <div class="col-md">
        <h4 class="mb-3"><i class="fas fa-plus"></i> Nuevo Plan de Compra: </h4>
        {{-- <p>Incluye Planes de Compras de mi Unidad Organizacional: <b>{{ Auth()->user()->organizationalUnit->name }}</p> --}}
    </div>
</div>

<br>

@livewire('purchase-plan.create-purchase-plan', [
    'action'                => 'store',
    'purchasePlanToEdit'    => '',
])

@endsection

@section('custom_js')

@endsection
