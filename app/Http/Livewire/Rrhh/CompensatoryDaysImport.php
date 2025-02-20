<?php

namespace App\Http\Livewire\Rrhh;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Models\Rrhh\CompensatoryDay;
use App\Imports\AbscencesImport as AbscencesImportFile;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\User;

class CompensatoryDaysImport extends Component
{
    use WithFileUploads;

    public $file;
    public $message2;
    public $non_existent_users;

    public function save()
    {
        set_time_limit(3600);
        ini_set('memory_limit', '1024M');

        $this->validate([
            'file' => 'required|file|max:10240', // 10MB Max
        ]);

        $file = $this->file;
        
        $tmpPath = $file->getRealPath(); //file is the livewire tmp path
        // $data = file_get_contents($tmpPath); //works just fine
        
        $csvData = file_get_contents($tmpPath);
        $lines = explode(PHP_EOL, $csvData);
        // dd($lines);
        $array = array();
        $count_inserts = 0;
        foreach ($lines as $key => $line) {
            $array[] = str_getcsv($line);
            if($array[$key][0]!=null){

                $rut = $array[$key][42];
                $dv = $array[$key][44];

                $request_date = Carbon::createFromFormat('!d/m/Y',$array[$key][48]);
                if(trim($array[$key][51])!=""){
                    
                    $start_date = Carbon::createFromFormat('d/m/Y H:i',$array[$key][49]." ".$array[$key][51]);
                }else{
                    $start_date = Carbon::createFromFormat('!d/m/Y',$array[$key][49]);
                }
                if(trim($array[$key][52])!=""){
                    $end_date = Carbon::createFromFormat('d/m/Y H:i',$array[$key][50]." ".$array[$key][52]);
                }else{
                    $end_date = Carbon::createFromFormat('!d/m/Y',$array[$key][50]);
                }
                $hrs = $array[$key][53];
                
                if(!User::find($rut)){
                    $this->non_existent_users[$rut] = $rut . "-" . $dv;
                }else{
                    CompensatoryDay::updateOrCreate([
                        'user_id' => $rut,
                        'request_date' => $request_date,
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ],[
                        'user_id' => $rut,
                        'request_date' => $request_date,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'hrs' => $hrs
                    ]);
    
                    $count_inserts += 1;
                } 
            }
        }

        $this->message2 = $this->message2 . 'Se ha cargado correctamente el archivo (' . $count_inserts . ' registros).';
    }

    public function render()
    {
        return view('livewire.rrhh.compensatory-days-import');
    }
}
