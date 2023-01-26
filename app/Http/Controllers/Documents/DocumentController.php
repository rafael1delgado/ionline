<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Documents\Document;
use App\Models\Documents\Signature;
use App\Models\Documents\SignaturesFile;
use App\Models\Documents\Correlative;
use App\Mail\SendDocument;
use App\Rrhh\OrganizationalUnit;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$users = User::Search($request->get('name'))->orderBy('name','Asc')->paginate(30);
        //$documents = Document::Search($request)->latest()->paginate(50);
        if (Auth()->user()->organizational_unit_id) {
            $childs = array(Auth()->user()->organizational_unit_id);

            $childs = array_merge($childs, Auth()->user()->OrganizationalUnit->childs->pluck('id')->toArray());
            foreach (Auth()->user()->OrganizationalUnit->childs as $child) {
                $childs = array_merge($childs, $child->childs->pluck('id')->toArray());
            }

            $ownDocuments = Document::with(
                'user',
                'user.organizationalUnit',
                'organizationalUnit',
                'fileToSign',
                'fileToSign.signaturesFlows'
                )
                ->Search($request)
                ->latest()
                ->where('user_id', Auth()->user()->id)
                //->whereIn('organizational_unit_id',$childs)
                // ->withTrashed()
                ->paginate(100);

            $otherDocuments = Document::with(
                'user',
                'user.organizationalUnit',
                'organizationalUnit',
                'fileToSign',
                'fileToSign.signaturesFlows'
                )
                ->Search($request)
                ->latest()
                ->where('user_id', '<>', Auth()->user()->id)
                ->where('type', '<>', 'Reservado')
                ->whereIn('organizational_unit_id', $childs)
                // ->withTrashed()
                ->paginate(100);

            return view('documents.index', compact('ownDocuments', 'otherDocuments', ));
        }
        else {
            return redirect()->back()->with('danger', 'Usted no posee asignada una unidad organizacional favor contactar a su administrador');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $document = new Document();
        $correlative_acta_menor = Correlative::where('type','Acta de Recepción Obras Menores')->first();
        //dd($correlative_acta_menor);
        //$correlative = new Document();
        return view('documents.create', compact('document','correlative_acta_menor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $document = new Document($request->All());
        $document->user()->associate(Auth::user());
        $document->establishment()->associate(auth()->user()->organizationalUnit->establishment);
        $document->organizationalUnit()->associate(Auth::user()->organizationalUnit);

        /* Agrega uno desde el correlativo */
        if (!$request->number) {
            if (
                $request->type == 'Memo' or
                $request->type == 'Acta de recepción' or
                $request->type == 'Circular' or
                $request->type == 'Acta de Recepción Obras Menores'
            ) {

                $document->number = Correlative::getCorrelativeFromType($request->type);
            }
        }
        $document->save();
        return redirect()->route('documents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Documents\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        if ($document->type == 'Acta de recepción') {
            return view('documents.reception')->withDocument($document);
        } else if ($document->type == 'Resolución') {
            return view('documents.resolution')->withDocument($document);
        } else if ($document->type == 'Circular') {
            //centrada la materia en negrita y sin de para
            return view('documents.circular')->withDocument($document);
        } else {
            /** TODO Temporal para diseñar las nuevas firmas */
            if($document->id == 13667) {
                return view('documents.show_13667')->withDocument($document);
            }
            return view('documents.show')->withDocument($document);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Documents\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        /* Si tiene número de parte entonces devuelve al index */
        if ($document->file) {
            session()->flash('danger', 'Lo siento, el documento ya tiene un archivo adjunto');
            return redirect()->route('documents.index');
        }
        /* De lo contrario retorna para editar el documento */ else {
            return view('documents.edit', compact('document'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Documents\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        $document->fill($request->all());
        /* Agrega uno desde el correlativo */
        if (!$request->number) {
            if (
                $request->type == 'Memo' or
                $request->type == 'Acta de recepción' or
                $request->type == 'Circular'
            ) {

                $document->number = Correlative::getCorrelativeFromType($request->type);
            }
        }
        /* Si no viene con número agrega uno desde el correlativo */
        //if(!$request->number and $request->type != 'Ordinario') {
        //    $document->number = Correlative::getCorrelativeFromType($request->type);
        //}
        $document->save();

        session()->flash('info', 'El documento ha sido actualizado.
            <a href="' . route('documents.show', $document->id) . '" target="_blank">
            Previsualizar</a>');

        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Documents\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Documents\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(Document $document)
    {
        Storage::disk('gcs')->delete($document->file);

        $document->file = null;
        $document->save();

        session()->flash('success', 'El archivo ha sido eliminado');
        return redirect()->route('documents.index');
    }

    public function addNumber()
    {
        return view('documents.add_number');
    }

    public function find(Request $request)
    {
        $document = Document::query()
            ->whereId($request->id)
            ->whereEstablishmentId( auth()->user()->organizationalUnit->establishment->id)
            ->first();
        return view('documents.add_number', compact('document'));
    }

    public function storeNumber(Request $request, Document $document)
    {
        $document->fill($request->all());

        if ($request->hasFile('file')) {
            $filename = $document->id . '-' .
                $document->type . '_' .
                $document->number . '.' .
                $request->file->getClientOriginalExtension();
            $document->file = $request->file->storeAs('ionline/documents/documents', $filename, ['disk' => 'gcs']);
        }
        $document->save();
        //unset($document->number);

        session()->flash('info', 'El documento ha sido actualizado.
            <a href="' . route('documents.show', $document->id) . '" target="_blank">
            Previsualizar</a>');

        if ($request->has('sendMail')) {
            /* Enviar a todos los email que aparecen en distribución */
            preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $document->distribution, $emails);
            //dd($emails[0]);
            Mail::to($emails[0])->send(new SendDocument($document));
        }

        return redirect()->route('documents.partes.outbox');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFromPrevious(Request $request)
    {
        $correlative_acta_menor = Correlative::where('type','Acta de Recepción Obras Menores')->first();
        $document = Document::findOrNew($request->document_id);
        $document->type = null;
        if ($document->user_id != Auth::id()) {
            $document = new Document();
        }

        return view('documents.create', compact('document','correlative_acta_menor'));
    }

    public function download(Document $document)
    {
        $filename = $document->type . ' ' .
            $document->number . '.' .
            File::extension($document->file);
        //return Storage::download($document->file, $filename);
        return Storage::disk('gcs')->response($document->file, $filename);
    }

    public function report()
    {
        $users = User::orderBy('name')->has('documents')->with('documents')->get();
        $ct = Document::count();
        $ous = OrganizationalUnit::has('documents')->get();
        return view('documents.report', compact('users', 'ct', 'ous'));
    }

    public function sendForSignature(Document $document)
    {
        $signature = new Signature();
        $signature->request_date = Carbon::now();
        $signature->subject = $document->subject;
        $signature->description = $document->antecedent;
        $signature->recipients = $document->distribution;

        switch ($document->type) {
            case 'Memo':
                $signature->document_type = 'Memorando';
                break;
            case 'Ordinario':
            case 'Reservado':
            case 'Oficio':
                $signature->document_type = 'Oficio';
                break;
            case 'Circular':
                $signature->document_type = 'Circular';
                break;
            case 'Acta de recepción':
                $signature->document_type = 'Acta';
                break;
            case 'Resolución':
                $signature->document_type = 'Resoluciones';
                break;
        }

        if ($signature->document_type = 'Memorando')

            //        $signature->endorse_type = 'Visación en cadena de responsabilidad';
            //        $signature->distribution = 'División de Atención Primaria MINSAL,Oficina de Partes SSI,'.$municipio;


            if ($document->type == 'Acta de recepción') {
                $image = base64_encode(file_get_contents(public_path('/images/logo_pluma.jpg')));
                $documentFile = \PDF::loadView('documents.reception', compact('document'));
            } else if ($document->type == 'Resolución') {
                $image = base64_encode(file_get_contents(public_path('/images/logo_rgb.png')));
                $documentFile = \PDF::loadView('documents.resolution', compact('document','image'));
            } else if ($document->type == 'Circular') {
                $image = base64_encode(file_get_contents(public_path('/images/logo_rgb.png')));
                $documentFile = \PDF::loadView('documents.circular', compact('document','image'));
            } else {
                $image = base64_encode(file_get_contents(public_path('/images/logo_rgb.png')));
                $documentFile = \PDF::loadView('documents.show', compact('document','image'));
            }

        $signaturesFile = new SignaturesFile();
        $signaturesFile->file = base64_encode($documentFile->output());
        $signaturesFile->file_type = 'documento';
        $signaturesFile->md5_file = md5($documentFile->output());

        $signature->signaturesFiles->add($signaturesFile);
        $documentId = $document->id;

        return view('documents.signatures.create', compact('signature', 'documentId'));
    }

    public function signedDocumentPdf($id)
    {
        $document = Document::find($id);
        return Storage::disk('gcs')->response($document->fileToSign->signed_file);
        //        header('Content-Type: application/pdf');
        //        if (isset($document->fileToSign)) {
        //            echo base64_decode($document->fileToSign->signed_file);
        //        }
    }
}
