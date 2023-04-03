<html lang="es">

@include('documents.templates.partials.head')

<body>
    <!-- Define header and footer blocks before your content -->
    @include('documents.templates.partials.header',[
        'linea1' => $document->organizationalUnit->name,
        'linea3' => "ID: " . $document->id . (isset($document->internal_number) ? '- Nº Interno: '. $document->internal_number : ''),
        'logo_pluma' => true
    ])

    @include('documents.templates.partials.footer')

    <!-- Define main for content -->
    <main>
        
        <div style="float: right; width: 300px; padding-top: 76px;">
            
            <div class="left quince" style="padding-left: 2px; padding-bottom: 10px;">
                <strong style="text-transform: uppercase; padding-right: 30px;">
                    {{ optional($document->type)->name }} N°:
                </strong> 
                <span class="catorce negrita">{{ $document->number }}</span>
            </div>

            @if($document->date)
            <div style="padding-top:5px; padding-left: 2px;">
                Iquique, {{ $document->date->day }} de {{ $document->date->monthName }} del {{ $document->date->year }}
            </div>
            @endif

        </div>

        <div style="clear: both; padding-bottom: 36px"></div>

        <div>
            {!! $document->contentHtml !!}
        </div>

        @include('documents.templates.partials.distribution')

        @include('documents.templates.partials.responsible')

    </main>

</body>

</html>