@extends('layouts.app')

@section('title', 'Editar ' . $control->type_format)

@section('content')

@include('warehouse.nav')

@livewire('warehouse.control.control-edit', [
    'store' => $store,
    'control' => $control,
    'type'  => ($control->type) ? 'receiving' : 'dispatch'
])

<hr>
<h4 class="my-2">Agregar Producto</h4>

@if($control->type)
    @livewire('warehouse.control.control-receiving-add-product', [
        'store' => $store,
        'control' => $control,
    ])
@else
    @livewire('warehouse.control.control-dispatch-add-product', [
        'store' => $store,
        'control' => $control,
    ])
@endif

<hr>
<h4>Productos agregados</h4>

@livewire('warehouse.control.control-product-list', [
    'control' => $control
])

@endsection
