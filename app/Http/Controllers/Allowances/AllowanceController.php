<?php

namespace App\Http\Controllers\Allowances;

use App\Http\Controllers\Controller;
use App\Models\Allowances\Allowance;
use App\Models\Allowances\AllowanceFile;
use App\Models\Parameters\AllowanceValue;
use App\Models\Allowances\AllowanceSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use App\Rrhh\Authority;
use App\Notifications\Allowances\NewAllowance;
use App\Models\Parameters\Parameter;
use App\User;
// use Illuminate\Http\RedirectResponse;

use App\Exports\AllowancesExport;
use Maatwebsite\Excel\Facades\Excel;

class AllowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view('allowances.index');
    }

    public function all_index()
    {   
        return view('allowances.all_index');
    }

    public function sign_index()
    {   
        return view('allowances.sign_index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $allowanceValues = AllowanceValue::where('year', Carbon::now()->year)->get();

        return view('allowances.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //CREAR PERIODOS DE FECHAS
        $period = CarbonPeriod::create($request->from, $request->to);
        $period = $period->toArray();
        
        foreach($period as $date){
            $currentAllowances = Allowance::where('user_allowance_id', $request->user_allowance_id)
                ->whereDate('from', '>=',$date)
                ->whereDate('to', '<=', end($period))
                ->get();
            
            if($currentAllowances->count() > 0){
                return back()->withInput($request->input())->with('error', 'El funcionario ya dispone de viático(s) para la fecha solicitada, favor consulta historial de funcionario');;
            }
            else{
                //SE ALMACENA VIATICO
                $allowance = new Allowance($request->All());
                $allowance->status = 'pending';
                $allowance->organizationalUnitAllowance()->associate($allowance->userAllowance->organizationalUnit);
                $allowance->allowanceEstablishment()->associate($allowance->userAllowance->organizationalUnit->establishment);
                $allowance->userCreator()->associate(Auth::user());
                $allowance->organizationalUnitCreator()->associate(Auth::user()->organizationalUnit);

                //CALCULO DE DIAS
                $allowance->total_days = $this->allowanceTotalDays($request);

                //VALOR DE VIATICO COMPLETO / MEDIO
                $value_by_degree = AllowanceValue::find($request->allowance_value_id);
                if($allowance->total_days >= 1){
                    $allowance->day_value = $value_by_degree->value;
                    $allowance->half_day_value = $value_by_degree->value * 0.4;
                }
                else{
                    $allowance->half_day_value = $value_by_degree->value * 0.4;
                }

                //TOTAL VIÁTICO
                $allowance->total_value = $this->allowanceTotalValue($allowance);

                $allowance->save();

                // SE ALMACENAN ARCHIVOS ADJUNTOS
                if($request->has('file')){
                    foreach ($request->file as $key_file => $file) {
                        $allowanceFile = new AllowanceFile();
                        $allowanceFile->name = $request->input('name.'.$key_file.'');
                        $id_file = $key_file + 1;
                        $file_name = 'id_'.$allowance->id.'_'.Carbon::now()->format('Y_m_d_H_i_s').'_'.$id_file;
                        $allowanceFile->file = $file->storeAs('/ionline/allowances/allowance_docs', $file_name.'.'.$file->extension(), 'gcs');

                        $allowanceFile->allowance()->associate($allowance);
                        $allowanceFile->user()->associate(Auth::user());
                        
                        $allowanceFile->save();
                    }
                }

                //SE AGREGA AL PRINCIPIO VISACIÓN SIRH
                $allowance_sing_sirh = new AllowanceSign();
                $allowance_sing_sirh->position = 1;
                $allowance_sing_sirh->event_type = 'sirh';
                $allowance_sing_sirh->status = 'pending';
                $allowance_sing_sirh->allowance_id = $allowance->id;
                $allowance_sing_sirh->organizational_unit_id = 40;
                $allowance_sing_sirh->save();

                //SE NOTIFICA PARA INICIAR EL PROCESO DE FIRMAS
                $notificationSirhPermissionUsers = User::permission('Allowances: sirh')->get();
                foreach($notificationSirhPermissionUsers as $notificationSirhPermissionUser){
                    $notificationSirhPermissionUser->notify(new NewAllowance($allowance));
                }

                //CONSULTO SI EL VIATICO ES PARA UNA AUTORIDAD
                $iam_authorities = Authority::getAmIAuthorityFromOu(Carbon::now(), 'manager', $allowance->userAllowance->id);

                //AUTORIDAD
                if($iam_authorities->isNotEmpty()){
                    foreach($iam_authorities as $iam_authority){
                        if($allowance->userAllowance->organizationalUnit->id == $iam_authority->organizational_unit_id){
                            //SE RESTA UNA U.O. POR SER AUTORIDAD
                            $level_allowance_ou = $iam_authority->organizationalUnit->level - 1;
                            
                            $nextLevel = $iam_authority->organizationalUnit->father;
                            $position = 2;

                            if($iam_authority->organizationalUnit->level == 2){
                                for ($i = $level_allowance_ou; $i >= 1; $i--){
                                    $allowance_sing = new AllowanceSign();
                                    $allowance_sing->position = $position;
                                    if($i >= 3){
                                        $allowance_sing->event_type = 'boss';
                                    }
                                    if($i == 2){
                                        $allowance_sing->event_type = 'sub-dir or boss';
                                    }
                                    if($i == 1){
                                        $allowance_sing->event_type = 'dir';
                                    }
                                    $allowance_sing->organizational_unit_id = $nextLevel->id;
                                    $allowance_sing->allowance_id = $allowance->id;

                                    $allowance_sing->save();

                                    $nextLevel = $allowance_sing->organizationalUnit->father;
                                    $position = $position + 1;
                                }
                            }
                            else{
                                for ($i = $level_allowance_ou; $i >= 2; $i--){
                                    $allowance_sing = new AllowanceSign();
                                    $allowance_sing->position = $position;
                                    if($i >= 3){
                                        $allowance_sing->event_type = 'boss';
                                    }
                                    if($i == 2){
                                        $allowance_sing->event_type = 'sub-dir or boss';
                                    }
                                    $allowance_sing->organizational_unit_id = $nextLevel->id;
                                    $allowance_sing->allowance_id = $allowance->id;

                                    $allowance_sing->save();

                                    $nextLevel = $allowance_sing->organizationalUnit->father;
                                    $position = $position + 1;
                                }
                            } 
                        }
                    }
                }
                //NO AUTORIDAD
                else{
                    $level_allowance_ou = $allowance->organizationalUnitAllowance->level;
                    $position = 2;

                    for ($i = $level_allowance_ou; $i >= 2; $i--){

                        $allowance_sign = new AllowanceSign();
                        $allowance_sign->position = $position;

                        if($i >= 3){
                            $allowance_sign->event_type = 'boss';
                            if($i == $level_allowance_ou){
                                $allowance_sign->organizational_unit_id = $allowance->organizationalUnitAllowance->id;
                            }
                            else{
                                $allowance_sign->organizational_unit_id = $nextLevel->id;
                            }
                            
                        }
                        if($i == 2){
                            $allowance_sign->event_type = 'sub-dir or boss';
                            if($i == $level_allowance_ou){
                                $allowance_sign->organizational_unit_id = $allowance->organizationalUnitAllowance->id;
                            }
                            else{
                                $allowance_sign->organizational_unit_id = $nextLevel->id;
                            }
                        }
                        
                        $allowance_sign->allowance_id = $allowance->id;

                        $allowance_sign->save();

                        $nextLevel = $allowance_sign->organizationalUnit->father;
                        $position = $position + 1;
                    }
                }

                //SE AGREGA AL FINAL JEFE FINANZAS
                $allowance_sing_finance = new AllowanceSign();
                $allowance_sing_finance->position = $position;
                $allowance_sing_finance->event_type = 'chief financial officer';
                $allowance_sing_finance->organizational_unit_id = Parameter::where('module', 'ou')->where('parameter', 'FinanzasSSI')->first()->value;
                $allowance_sing_finance->allowance_id = $allowance->id;
                $allowance_sing_finance->save();

                session()->flash('success', 'Estimados Usuario, se ha creado exitosamente la solicitud de viatico N°'.$allowance->id);
                return redirect()->route('allowances.index');
            }
        }
    }

    public function allowanceTotalDays($request){
        if($request->from == $request->to){
            return 0.5;
        }
        else{
            return Carbon::parse($request->from)->diffInDays(Carbon::parse($request->to)) + 0.5;
        }
    }

    public function allowanceTotalValue($allowance){
        $total_int_days = intval($allowance->total_days);
        if($total_int_days >= 1){
            return ($allowance->day_value * $total_int_days) + $allowance->half_day_value;
        }
        else{
            return $allowance->half_day_value;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Allowance  $allowance
     * @return \Illuminate\Http\Response
     */
    public function show(Allowance $allowance)
    {
        return view('allowances.show', compact('allowance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Allowance  $allowance
     * @return \Illuminate\Http\Response
     */
    public function edit(Allowance $allowance)
    {
        /*
        $allowanceValues = AllowanceValue::where('year', Carbon::now()->year)->get();
        return view('allowances.edit', compact('allowance', 'allowanceValues'));
        */
        return view('allowances.edit', compact('allowance'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Allowance  $allowance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Allowance $allowance)
    {
        $allowance->fill($request->All());
        $allowance->organizationalUnitAllowance()->associate($allowance->userAllowance->organizationalUnit);
        $allowance->allowanceEstablishment()->associate($allowance->userAllowance->organizationalUnit->establishment);
        $allowance->userCreator()->associate(Auth::user());
        $allowance->organizationalUnitCreator()->associate(Auth::user()->organizationalUnit);

        //CALCULO DE DIAS
        $allowance->total_days = $this->allowanceTotalDays($request);

        //VALOR DE VIATICO COMPLETO / MEDIO
        $value_by_degree = AllowanceValue::find($request->allowance_value_id);
        if($allowance->total_days >= 1){
            $allowance->day_value = $value_by_degree->value;
            $allowance->half_day_value = $value_by_degree->value * 0.4;
        }
        else{
            $allowance->half_day_value = $value_by_degree->value * 0.4;
        }

        //TOTAL VIÁTICO
        $allowance->total_value = $this->allowanceTotalValue($allowance);

        $allowance->save();

        if($request->has('file')){
            foreach ($request->file as $key_file => $file) {
                $allowanceFile = new AllowanceFile();
                $allowanceFile->name = $request->input('name.'.$key_file.'');
                $id_file = $key_file + 1;
                $file_name = 'id_'.$allowance->id.'_'.Carbon::now()->format('Y_m_d_H_i_s').'_'.$id_file;
                $allowanceFile->file = $file->storeAs('/ionline/allowances/allowance_docs', $file_name.'.'.$file->extension(), 'gcs');

                $allowanceFile->allowance()->associate($allowance);
                $allowanceFile->user()->associate(Auth::user());
                
                $allowanceFile->save();
            }
        }

        session()->flash('success', 'Estimado Usuario, se ha editado exitosamente la solicitud de viatico N°'.$allowance->id);
        return redirect()->route('allowances.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Allowance  $allowance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Allowance $allowance)
    {
        //
    }

    public function show_file(Allowance $allowance)
    {
        return Storage::disk('gcs')->response($allowance->signedAllowance->signed_file);
    }

    //REPORTS
    public function create_by_dates()
    {
        return view('allowances.reports.create_by_dates');
        //return Excel::download(new AllowancesExport, 'listado-viaticos.xlsx');
    }

    public function create_by_dates_excel($from, $to)
    {
        return Excel::download(new AllowancesExport($from, $to), 'listado-viaticos.xlsx');
    }

    public function import(){
        return view('allowances.import.import');
    }
}
