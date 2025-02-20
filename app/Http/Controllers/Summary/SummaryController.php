<?php

namespace App\Http\Controllers\Summary;

use Illuminate\Http\Request;
use App\Models\Summary\Type;
use App\Models\Summary\Summary;
use App\Models\Summary\EventType;
use App\Models\Summary\Event;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('Summary: user')) {
            $user_id = auth()->id();
            $summaries = Summary::where('investigator_id', $user_id)
                ->orWhere('actuary_id', $user_id)
                ->orWhere('creator_id', $user_id)
                ->latest()
                ->get();
        }

        if (Auth::user()->can('Summary: admin') or Auth::user()->can('Summary: admin viewer') or Auth::user()->can('be god')) {
            $summaries = Summary::where('establishment_id', auth()->user()->organizationalUnit->establishment->id)
                ->latest()
                ->get();
        }




        return view('summary.index', compact('summaries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = Type::pluck('name', 'id');

        $eventType = EventType::where('start', true)->first();

        if (is_null($eventType)) {
            session()->flash('danger', 'No existe ningún tipo de evento marcado como: "Es el primer evento de un sumario"');
            return redirect()->back();
        } else {
            return view('summary.create', compact('types'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /** Obtiene el evento marcado como primer evento de un sumario */
        $eventType = EventType::where('start', true)
            ->where('summary_type_id', $request->input('type_id'))
            ->first();

        if (is_null($eventType)) {
            session()->flash('warning', 'No existe ningún tipo de evento marcado como: "Es el primer evento de un sumario para este tipo de investigación"');
        } else {
            $summary = new Summary($request->All());
            $summary->creator_id = auth()->user()->id;
            /** El estado lo sacaremos de lastEvent */
            // $summary->status = $eventType->name;
            $summary->start_at = now();
            $summary->establishment_id = auth()->user()->organizationalUnit->establishment->id;
            $summary->save();

            $event = new Event();
            $event->event_type_id = $eventType->id;
            $event->start_date = now();
            $event->summary_id = $summary->id;
            $event->creator_id = auth()->id();
            $event->save();

            session()->flash('success', 'Se creo el sumario correctamente.');
        }

        return redirect()->route('summary.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Summary $summary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function edit(Summary $summary)
    {
        return view('summary.edit', compact('summary'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
