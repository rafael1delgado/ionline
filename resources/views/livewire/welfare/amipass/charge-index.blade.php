<div>
    @if($records->count() > 0)
    <table class="table table-sm table-bordered table-hover" style="font-size: 12px;">
        <thead>
            <tr>
                <th width="95px" scope="col">Rut</th>
                <th scope="col">Funcionario</th>
                <th scope="col">Lugar desempeño</th>
                <th scope="col">Fecha registro</th>
                <th scope="col">Total real cargado</th>
                <th scope="col">Días de ausentismos</th>
                <th scope="col">Días hábiles del mes</th>
                <th scope="col">Días a cargar</th>
                <th scope="col">Valor día</th>
                <th scope="col">Valor que debía cargarse</th>
                <th scope="col">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->rut }}-{{ $record->dv }}</td>
                <td>{{ $record->nombre }}</td>
                <td>{{ $record->lugar_desempeño }}</td>
                <td>{{ Str::after($record->fecha, '-') }}</td>
                <td class="text-right">{{ number_format($record->total_real_cargado, 0, ",", ".") ?? '-' }}</td>
                <td class="text-right">{{ $record->dias_ausentismo ?? '-' }}</td>
                <td class="text-right">{{ $record->dias_habiles_mes ?? '-' }}</td>
                <td class="text-right">{{ $record->dias_a_cargar ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->valor_dia, 0, ",", ".") ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->valor_debia_cargarse, 0, ",", ".") ?? '-' }}</td>
                <td class="{{$record->diferencia_color}} text-right">{{ number_format($record->diferencia, 0, ",", ".") ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-weight-bold text-right">
                <td colspan="4" class="text-right">Totales $</td>
                <td>{{number_format($records->sum('total_real_cargado'), 0, ",", ".")}}</td>
                <td>{{$records->sum('dias_ausentismo')}}</td>
                <td>{{$records->sum('dias_habiles_mes')}}</td>
                <td>{{$records->sum('dias_a_cargar')}}</td>
                <td></td>
                <td>{{number_format($records->sum('valor_debia_cargarse'), 0, ",", ".")}}</td>
                <td class="{{ $records->sum('diferencia') > 0 ? 'text-success' : 'text-danger' }}">{{number_format($records->sum('diferencia'), 0, ",", ".")}}</td>
            </tr>
        </tfoot>
    </table>

    <div class="alert alert-info" role="alert">
        A la fecha de Agosto-23 presentaba un saldo {{$records->sum('diferencia') > 0 ? 'a favor' : 'en contra' }} de ${{number_format(abs($records->sum('diferencia')), 0, ",", ".")}} entre todas las cargas autorizadas el cual será regularizada en la(s) próxima(s) carga(s) programada(s).
    </div>
    @else
    <fieldset>No presenta registros de cargas efectivas en Amipass</fieldset>
    @endif

    @if($regularizations->count() > 0)
    <table class="table table-sm table-bordered table-hover" style="font-size: 12px;">
        <thead>
            <tr>
                <th colspan="5">Regularizaciones registradas</th>
            </tr>
            <tr>
                <th width="95px" scope="col">Rut</th>
                <th scope="col">Funcionario</th>
                <th scope="col">Lugar desempeño</th>
                <th scope="col">Fecha registro</th>
                <th scope="col">Total regularizado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($regularizations as $record)
            <tr>
                <td>{{ $record->rut }}-{{ $record->dv }}</td>
                <td>{{ $record->nombre }}</td>
                <td>{{ $record->lugar_desempeño }}</td>
                <td>{{ Str::after($record->fecha, '-') }}</td>
                <td class="{{ $record->total_real_cargado > 0 ? 'text-success' : 'text-danger' }} font-weight-bold text-right">{{ number_format($record->total_real_cargado, 0, ",", ".") ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <fieldset>No presenta registros de regularizaciones</fieldset>
    @endif

    @if($new_records->count() > 0)
    <table class="table table-sm table-bordered table-hover" style="font-size: 12px;">
        <thead>
            <tr>
                <th colspan="10">Mis cargas de Oct-23 a la fecha</th>
            </tr>
            <tr>
                <th width="95px" scope="col">Rut</th>
                <th scope="col">Funcionario</th>
                <th scope="col">Lugar desempeño</th>
                <th scope="col">Fecha registro</th>
                <th scope="col">Días hábiles del mes</th>
                <th scope="col">Días de ausentismos</th>
                <th scope="col">Valor día</th>
                <th scope="col">Subtotal mes</th>
                <th scope="col">Valor regularizado</th>
                <th scope="col">Valor a cargar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($new_records as $record)
            <tr>
                <td>{{ $record->rut }}-{{ $record->dv }}</td>
                <td>{{ $record->nombre }}</td>
                <td>{{ $record->lugar_desempeño }}</td>
                <td>{{ Str::after($record->fecha, '-') }}</td>
                <td class="text-right">{{ $record->dias_habiles_mes ?? '-' }}</td>
                <td class="text-right">{{ $record->dias_ausentismo ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->valor_dia, 0, ",", ".") ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->subtotal, 0, ",", ".") ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->total_regularizado, 0, ",", ".") ?? '-' }}</td>
                <td class="text-right font-weight-bold">{{ number_format($record->valor_a_cargar, 0, ",", ".") ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <fieldset>No presenta registros de cargas efectivas en Amipass</fieldset>
    @endif
    
</div>
