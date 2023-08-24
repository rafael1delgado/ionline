<div>
    <style>
        .bg-image {
            width: 340px;
            height: 340px;
            border: 2px solid #ccc;
            background-image: url('{{ asset('images/inventario_HAH_nuevo.png') }}');
            background-size: 340px;
        }
        .qr {
            padding-top: 111px;
            padding-left: 6px;
        }
        .code {
            padding-top: 16px;
            padding-left: 6px;
            text-align: center;
            font-size: 14px; 
            font-weight: bold; 
            
            
        }
    </style>

    <div class="bg-image">
        <div class="qr text-center">
            {!! $inventory->qr !!}
        </div>
        <div class="code">
            {{ $inventory->number }}
        </div>
    </div>
    
    Acá se verá la información del inventario de este item
    <hr>
</div>
