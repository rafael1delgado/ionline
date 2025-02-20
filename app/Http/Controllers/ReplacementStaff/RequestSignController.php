<?php

namespace App\Http\Controllers\ReplacementStaff;

use App\Models\ReplacementStaff\RequestSign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Rrhh\Authority;
use Illuminate\Support\Facades\Mail;
// use App\Mail\NotificationSign;
use App\Notifications\ReplacementStaff\NotificationSign;
use App\Notifications\ReplacementStaff\NotificationRejectedRequest;
use App\Notifications\ReplacementStaff\NotificationEndSigningProcess;
use App\Models\ReplacementStaff\RequestReplacementStaff;
// use App\Mail\NotificationEndSigningProcess;
use App\User;
use App\Models\Parameters\Parameter;
use App\Models\Profile\Subrogation;
use App\Services\SignatureService;
use App\Notifications\ReplacementStaff\NotificationFinanceElectronicSign;
use App\Models\Documents\Signature;

class RequestSignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RequestSign  $requestSing
     * @return \Illuminate\Http\Response
     */
    public function show(RequestSign $requestSign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RequestSign  $requestSing
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestSign $requestSign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RequestSign  $requestSing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestSign $requestSign, $status, RequestReplacementStaff $requestReplacementStaff)
    {
        if($status == 'accepted'){
            $requestSign->user_id = Auth::user()->id;
            $requestSign->request_status = $status;
            $requestSign->date_sign = Carbon::now();
            $requestSign->save();

            if($request->has('budget_item_id')){
                $requestReplacementStaff->budget_item_id = $request->budget_item_id;
                $requestReplacementStaff->save();
            }

            $nextRequestSign = $requestSign->requestReplacementStaff->requestSign->where('position', $requestSign->position + 1)->first();

            if($nextRequestSign && 
                $nextRequestSign->ou_alias == 'finance' && 
                    $requestReplacementStaff->form_type == 'replacement'){
                /* Se crea solicitu de firma electrónica */
                $signature = Subrogation::
                    where('organizational_unit_id',  Parameter::where('parameter', 'FinanzasSSI')->first()->value)
                    ->where('type', 'manager')
                    ->where('level', 1)
                    ->orderBy('level', 'asc')
                    ->first();
                
                if($signature){
                    /* SE REGISTRA COMO PENDIENTE LA PROXIMA APROBACION DE FINANZAS */     
                    $nextRequestSign->request_status = 'not valid';
                    $nextRequestSign->save();

                    /* SE CREAN FIRMAS DIGITALES */
                    $signatureFinance = new SignatureService();

                    $signatureFinance->addResponsible($requestSign->requestReplacementStaff->requesterUser);

                    $signatureFinance->addSignature(
                        5,
                        'Certificado de disponibilidad presupuestaria',
                        'Solicitud de reemplazo ID: '. $requestSign->requestReplacementStaff->id.'<br><br>'.
                        '<small><b>Periodo</b>: '. $requestSign->requestReplacementStaff->start_date->format('d-m-Y').' - '.$requestSign->requestReplacementStaff->end_date->format('d-m-Y').'<br>'.
                        '<b>Funcionario</b>: '. $requestSign->requestReplacementStaff->name_to_replace.'<br>'.
                        '<b>'.$requestSign->requestReplacementStaff->budgetItem->code.'</b> - '.$requestSign->requestReplacementStaff->budgetItem->name.'</small>',
                        'No requiere visación',
                        true
                    );
                    
                    $signatureFinance->addView('replacement_staff.request.documents.budget_availability_certificate', [
                        'requestReplacementStaff' => $requestSign->requestReplacementStaff
                    ]);

                    $signatureFinance->addVisators(collect([]));
                    $signatureFinance->addSignatures(collect([$signature->user]));
                    
                    $signatureFinance = $signatureFinance->sendRequest();

                    /* CONSULTA POR SIGNATURE RECIENTE */
                    $currentSignatureId = Signature::where('id', $signatureFinance->id)->first()->signaturesFiles->first()->id;

                    $requestSign->requestReplacementStaff->signaturesFile()->associate($currentSignatureId);

                    $requestSign->requestReplacementStaff->save();

                    /* SE ENVÍA NOTIFIACIÓN SOBRE FIRMAS ELECTRÓNICAS */
                    $signature->user->notify(new NotificationFinanceElectronicSign($nextRequestSign->requestReplacementStaff));
                    
                    session()->flash('success', 'Su solicitud ha sido Aceptada con exito.');
                    return redirect()->route('replacement_staff.request.to_sign_index');
                }
                else{
                    session()->flash('danger', 'Estimado Usuario: No es posible aprobar solicitudes debido a que no se 
                    encuentra configurada la autoridad de Departamento de Gestión Financiera');
                    return redirect()->route('replacement_staff.request.to_sign_index');
                }
            }
            if(($nextRequestSign && 
                $nextRequestSign->ou_alias != 'finance' &&
                    $requestReplacementStaff->form_type == 'replacement') || 
                        ($nextRequestSign && $requestReplacementStaff->form_type == 'announcement')){
                // $nextRequestSign = $requestSign->requestReplacementStaff->requestSign->where('position', $requestSign->position + 1)->first();
                $nextRequestSign->request_status = 'pending';
                $nextRequestSign->save();

                /* FIX: @mirandaljorge si no hay manager en Authority, se va a caer */
                $notification_ou_manager = Authority::getAuthorityFromDate($nextRequestSign->organizational_unit_id, $requestSign->date_sign, 'manager');
                $users = [$notification_ou_manager->user]; 

                if($nextRequestSign->ou_alias == 'uni_per'){
                    $personal_users = User::latest()
                        ->whereHas('roles', function($q){
                            $q->Where('name', 'Replacement Staff: personal sign');
                        })
                        ->get();
                    foreach ($personal_users as $key => $personal_user) {
                        array_push($users , $personal_user);
                    }
                }

                // AQUI ENVIAR NOTIFICACIÓN DE AL NUEVO VISADOR.
                foreach ($users as $key => $user) {
                    $user->notify(new NotificationSign($requestReplacementStaff));
                }

                session()->flash('success', 'Su solicitud ha sido Aceptada con exito.');
                return redirect()->route('replacement_staff.request.to_sign_index');
            }
            if(!$nextRequestSign){
                // NOTIFICACION PARA RECLUTAMIENTO
                $notification_reclutamiento_manager = Authority::getAuthorityFromDate(Parameter::where('module', 'ou')->where('parameter', 'ReclutamientoSSI')->first()->value, today(), 'manager');
                if($notification_reclutamiento_manager){
                    $notification_reclutamiento_manager->user->notify(new NotificationEndSigningProcess($requestReplacementStaff));
                }
                session()->flash('success', 'Su solicitud ha sido Aceptada en su totalidad.');
                return redirect()->route('replacement_staff.request.to_sign_index');
            }
        }
        else{
            $requestSign->user_id = Auth::user()->id;
            $requestSign->request_status = $status;
            $requestSign->observation = $request->observation;
            $requestSign->date_sign = Carbon::now();
            $requestSign->save();

            $requestReplacementStaff->request_status = 'rejected';
            $requestReplacementStaff->save();

            //SE NOTIFICA A UNIDAD DE RECLUTAMIENTO
            $notification_reclutamiento_manager = Authority::getAuthorityFromDate(48, today(), 'manager');
            if($notification_reclutamiento_manager){
                $notification_reclutamiento_manager->user->notify(new NotificationRejectedRequest($requestReplacementStaff, 'reclutamiento'));
            }
            $requestReplacementStaff->requesterUser->notify(new NotificationRejectedRequest($requestReplacementStaff, 'requester'));
            $requestReplacementStaff->user->notify(new NotificationRejectedRequest($requestReplacementStaff, 'user'));

            session()->flash('danger', 'Su solicitud ha sido Rechazada con éxito.');
            return redirect()->route('replacement_staff.request.to_sign_index');
        }

        // session()->flash('success', 'Su solicitud ha sido.');
        // return redirect()->route('replacement_staff.edit', $replacementStaff);
    }

    public function massive_update(Request $request)
    {
        $signature = Subrogation::
            where('organizational_unit_id',  Parameter::where('parameter', 'FinanzasSSI')->first()->value)
            ->where('type', 'manager')
            ->where('level', 1)
            ->orderBy('level', 'asc')
            ->first();
        
        if($signature){
            foreach($request->sign_id as $sign_id){
                $sign = RequestSign::where('id', $sign_id)->first();
                if($sign->ou_alias == "sub_rrhh"){
                    $sign->user_id = Auth::user()->id;
                    $sign->request_status = 'accepted';
                    $sign->date_sign = now();
                    $sign->save();
                }
    
                $nextRequestSign = $sign->requestReplacementStaff->requestSign->where('position', $sign->position + 1)->first();
                
                if($nextRequestSign){
                    /* SE REGISTRA COMO PENDIENTE LA PROXIMA APROBACION DE FINANZAS */       
                    $nextRequestSign->request_status = 'not valid';
                    $nextRequestSign->save();
    
                    /* SE CREAN FIRMAS DIGITALES */
                    $signatureFinance = new SignatureService();
    
                    $signatureFinance->addResponsible($sign->requestReplacementStaff->requesterUser);
    
                    $signatureFinance->addSignature(
                        5,
                        'Certificado de disponibilidad presupuestaria',
                        'Solicitud de reemplazo ID: '. $sign->requestReplacementStaff->id.'<br><br>'.
                        '<small><b>Periodo</b>: '. $sign->requestReplacementStaff->start_date->format('d-m-Y').' - '.$sign->requestReplacementStaff->end_date->format('d-m-Y').'<br>'.
                        '<b>Funcionario</b>: '. $sign->requestReplacementStaff->name_to_replace.'<br>'.
                        '<b>'.$sign->requestReplacementStaff->budgetItem->code.'</b> - '.$sign->requestReplacementStaff->budgetItem->name.'</small>',
                        'No requiere visación',
                        true
                    );
    
                    $signatureFinance->addView('replacement_staff.request.documents.budget_availability_certificate', [
                        'requestReplacementStaff' => $sign->requestReplacementStaff
                    ]);
    
                    $signatureFinance->addVisators(collect([]));
                    $signatureFinance->addSignatures(collect([$signature->user]));

                    //$signatureFinance->url = route('replacement_staff.request.show', $sign->requestReplacementStaff->id);
                    
                    $signatureFinance = $signatureFinance->sendRequest();

                    /* CONSULTA POR SIGNATURE RECIENTE */
                    $currentSignatureId = Signature::where('id', $signatureFinance->id)->first()->signaturesFiles->first()->id;

                    // dd($currentSignatureId);
    
                    $sign->requestReplacementStaff->signaturesFile()->associate($currentSignatureId);

                    $sign->requestReplacementStaff->save();

                    /* SE ENVÍA NOTIFIACIÓN SOBRE FIRMAS ELECTRÓNICAS */
                    $signature->user->notify(new NotificationFinanceElectronicSign($nextRequestSign->requestReplacementStaff));
                }
            }
    
            session()->flash('success', 'Estimado Usuario: Sus solicitud(es) ha(n) sido Aceptada(s).');
            return redirect()->route('replacement_staff.request.to_sign_index');
        }
        else{
            session()->flash('danger', 'Estimado Usuario: No es posible aprobar solicitudes debido a que no se 
            encuentra configurada la autoridad de Departamento de Gestión Financiera');
            return redirect()->route('replacement_staff.request.to_sign_index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequestSign  $requestSing
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestSign $requestSign)
    {
        //
    }
}
