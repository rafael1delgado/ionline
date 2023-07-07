<?php

namespace App\Http\Controllers\Welfare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Welfare\Abscence;
use App\Models\Welfare\EmployeeInformation;
use App\Models\Welfare\Doubt;

class AmipassController extends Controller
{
    public function index()
    {
        $employeeInformations = EmployeeInformation::paginate(50);

        return view('welfare.amipass.dashboard', compact('employeeInformations'));
    }

    public function questionMyIndex()
    {

        $questionerId = auth()->id();
        $doubts = Doubt::where('questioner_id', $questionerId)->get();
        return view('welfare.amipass.questionmyindex', compact('doubts'));
    }

    public function questionAllIndex()
    {
        $doubts = Doubt::all();
        return view('welfare.amipass.questionallindex', compact('doubts'));
    }

    public function questionCreate()
    {
        $user = auth()->user();
        return view('welfare.amipass.questioncreate', compact('user'));
    }


    public function questionStore(Request $request)
    {
        // Obtener los datos del formulario
        $nombreCompleto = $request->input('nombre_completo');
        $rut = $request->input('rut');
        $correo = $request->input('correo');
        $establecimiento = $request->input('establecimiento');
        $motivo = $request->input('motivo');
        $consulta = $request->input('consulta');


        $questionerId = auth()->id();


        Doubt::create([
            'nombre_completo' => $nombreCompleto,
            'rut' => $rut,
            'correo' => $correo,
            'establecimiento' => $establecimiento,
            'motivo' => $motivo,
            'consulta' => $consulta,
            'respuesta' => null,
            'questioner_id' => $questionerId,
            'question_at' => now(),
            'answerer_id' => null, // Dejarlo como null inicialmente
            'answer_at' => null, // Dejarlo como null inicialmente
        ]);

        session()->flash('success', 'Consulta de AmiPass ingresada exitosamente');


        return redirect()->route('welfare.amipass.question-my-index');
    }


    public function questionEdit($id)
    {
        $doubt = Doubt::findOrFail($id);
        return view('welfare.amipass.questionedit', compact('doubt'));
    }

    public function questionUpdate(Request $request, $id)
    {
        $doubt = Doubt::findOrFail($id);

        $doubt->respuesta = $request->input('respuesta');
        $doubt->answerer_id = auth()->id();
        $doubt->answer_at = now();

        $doubt->save();

        session()->flash('success', 'Respuesta actualizada exitosamente');

        return redirect()->route('welfare.amipass.question-all-index');
    }
}
