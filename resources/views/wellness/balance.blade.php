@extends('layouts.app')

@section('title', 'Contenido DOS')

@section('content')

@include('wellness.nav')

<div class="row mb-3">
    <div class="col-md-12 text-right">
        <a href="{{ route('wellness.exportBalance') }}" class="btn btn-success">
            <i class="fa fa-file-excel"></i> Descargar Resultado con Formato
        </a>
        <a id="downloadLinkExcel" onclick="exportF(this)" class="btn btn-success">
            <i class="fa fa-file-excel"></i> Descargar Resultado en Pantalla en Excel
        </a>
    </div>
</div>

<table class="table table-bordered" id="tabla_balance">
    <thead>
        <tr>            
            <th>Título</th>
            <th>Ítem</th>
            <th>Asignación</th>
            <th>Glosa</th>
            <th>Presupuesto Inicial</th>
            <th>Modificaciones Mes XXX</th>
            <th>Presupuesto Ajustado</th>
            <th>Presupuesto Ejecutado</th>
            <th>Saldo Presupuesto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($balances as $balance)

        <tr @if($balance->item == '000')  class="table-secondary"  @endif>
            <td>{{ $balance->titulo }}</td>
            <td>{{ $balance->item }}</td>
            <td>{{ $balance->asignacion }}</td>
            <td>{{ $balance->glosa }}</td>
            <td class="text-right">{{ money($balance->inicial) }}</td>
            <td class="text-right @if($balance->traspaso <0) text-danger @endif">{{ money($balance->traspaso) }}</td>
            <td class="text-right">{{ money($balance->ajustado) }}</td>
            <td class="text-right">{{ money($balance->ejecutado) }}</td>
            <td class="text-right">{{ money($balance->saldo) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection



<script type="text/javascript">
    function exportF(elem) {
        var table = document.getElementById("tabla_balance");
        var html = table.outerHTML;
        var html_no_links = html.replace(/<a[^>]*>|<\/a>/g, ""); //remove if u want links in your table
        var url = 'data:application/vnd.ms-excel,' + escape(html_no_links); // Set your html table into url
        elem.setAttribute("href", url);
        elem.setAttribute("download", "tabla_balance.xls"); // Choose the file name
        return false;
    }
</script>