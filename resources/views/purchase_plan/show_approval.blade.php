<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

<div class="row">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="fas fa-shopping-cart"></i> Plan de Compra: ID {{ $purchasePlan->id }}
            @switch($purchasePlan->status)
                @case('save')
                    <span class="badge bg-primary badge-sm">Guardado</span>
                    @break
            
                @case('sent')
                    <span class="badge bg-secondary badge-sm">Enviado</span>
                    @break
                @default
                    ''
            @endswitch
        </h5>
    </div>
</div>

<br>

<h6><i class="fas fa-info-circle"></i> 1. Descripción</h6>
<div class="table-responsive">
    <table class="table table-bordered table-sm small">
        <thead>
            <tr>
                <th width="30%" class="table-secondary">Asunto</th>
                <td class="text-left">{{ $purchasePlan->subject }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Descripción general del proyecto o adquisición</th>
                <td class="text-left">{{ $purchasePlan->description }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Propósito general del proyecto o adquisición</th>
                <td class="text-left">{{ $purchasePlan->purpose }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Funcionario Responsable</th>
                <td class="text-left">{{ $purchasePlan->userResponsible->FullName }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Cargo</th>
                <td class="text-left">{{ $purchasePlan->position }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Teléfono</th>
                <td>{{ $purchasePlan->telephone }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Correo Electronico</th>
                <td>{{ $purchasePlan->email }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Depto./Unidad</th>
                <td class="text-left">{{ $purchasePlan->organizationalUnit->name }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Nombre del Programa o Presupuesto Designado</th>
                <td>{{ $purchasePlan->program }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Monto Solicitado (Aproximado)</th>
                <td>${{ number_format($purchasePlan->estimated_expense, 0, ",", ".") }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Monto aprobado</th>
                <td></td>
            </tr>
        </thead>
    </table>
</div>

<br>

<div class="row"> 
    <div class="col">
        <h6><i class="fas fa-info-circle"></i> 2. Ítems a comprar</h6>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-sm small">
    <thead>
            <tr class="text-center">
                <th width="" class="table-secondary" rowspan="2">#</th>
                <th width="" class="table-secondary" rowspan="2">Artículo</th>
                <th width="" class="table-secondary" rowspan="2">UM</th>
                <th width="" class="table-secondary" rowspan="2">Especificaciones Técnicas</th>
                <th width="" class="table-secondary" colspan="2">Cantidad</th>
                <th width="" class="table-secondary" rowspan="2">Valor U.</th>
                <th width="" class="table-secondary" rowspan="2">Impuestos</th>
                <th width="" class="table-secondary" rowspan="2">Total Item</th>
            </tr>
            <tr>
                <th class="table-secondary">Solicitados</th>
                <th class="table-secondary">Programados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchasePlan->purchasePlanItems as $item)
            <tr class="text-center">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->unspscProduct->name }}</td>
                <td>{{ $item->unit_of_measurement }}</td>
                <td>{{ $item->specification }}</td>
                <td>{{ $item->quantity }}</td>
                <td class="{{ $item->quantity > $item->scheduled_quantity ? 'text-danger' : 'text-success' }}">{{ $item->scheduled_quantity }}</td>
                <td class="text-end">${{ number_format($item->unit_value, 0, ",", ".") }}</td>
                <td>{{ $item->tax }}</td>
                <td class="text-end">${{ number_format($item->expense, 0, ",", ".") }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7"></td>
                <th class="text-end">Total</th>
                <th class="text-end">${{ number_format($purchasePlan->estimated_expense, 0, ",", ".") }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<br>

<div class="table-responsive">
    <table class="table table-bordered table-sm small">
        <thead>
            <tr class="text-center">
                <th class="table-secondary" rowspan="2">#</th>
                <th class="table-secondary" rowspan="2">Artículo</th>
                <th class="table-secondary" rowspan="2">UM</th>
                <th class="table-secondary" rowspan="2">Cantidad</th>
                <th class="table-secondary" colspan="12">Cantidad programadas por meses</th>
            </tr>
            <tr class="text-center">
                <th class="table-secondary">Ene</th>
                <th class="table-secondary">Feb</th>
                <th class="table-secondary">Mar</th>
                <th class="table-secondary">Abr</th>
                <th class="table-secondary">May</th>
                <th class="table-secondary">Jun</th>
                <th class="table-secondary">Jul</th>
                <th class="table-secondary">Ago</th>
                <th class="table-secondary">Sep</th>
                <th class="table-secondary">Oct</th>
                <th class="table-secondary">Nov</th>
                <th class="table-secondary">Dic</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchasePlan->purchasePlanItems as $item)
            <tr class="text-center">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->unspscProduct->name }}</td>
                <td>{{ $item->unit_of_measurement }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->january ?? 0 }}</td>
                <td>{{ $item->february ?? 0 }}</td>
                <td>{{ $item->march ?? 0 }}</td>
                <td>{{ $item->april ?? 0 }}</td>
                <td>{{ $item->may ?? 0 }}</td>
                <td>{{ $item->june ?? 0 }}</td>
                <td>{{ $item->july ?? 0 }}</td>
                <td>{{ $item->august ?? 0 }}</td>
                <td>{{ $item->september ?? 0 }}</td>
                <td>{{ $item->october ?? 0 }}</td>
                <td>{{ $item->november ?? 0 }}</td>
                <td>{{ $item->december ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<br>

<div class="row"> 
    <div class="col">
        <h6><i class="fas fa-info-circle"></i> 3. Aprobaciones</h6>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-sm small">
        <thead>
            <tr class="text-center">
                @foreach($purchasePlan->approvals as $approval)
                <th width="" class="table-secondary">{{ $approval->sentToOu->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                @foreach($purchasePlan->approvals as $approval)
                <td>
                    @switch($approval->StatusInWords)
                        @case('Pendiente')
                            <i class="fas fa-clock"></i> {{ $approval->StatusInWords }}
                            @break
                    @endswitch

                    {{ ($approval->approver) ? $approval->approver->FullName : '' }} <br>
                    {{ ($approval->approver_at) ? $approval->approver_at->format('d-m-Y H:i:s') : '' }}
                </td>           
                @endforeach
            </tr>
        <tbody>
    </table>
</div>

{{--
@endsection
--}}