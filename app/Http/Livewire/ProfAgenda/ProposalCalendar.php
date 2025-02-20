<?php

namespace App\Http\Livewire\ProfAgenda;

use Livewire\Component;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Models\ProfAgenda\Proposal;

class ProposalCalendar extends Component
{
    public $events = '';
    public $proposal;

    protected $listeners = ['update_calendar' => 'update_calendar'];
 
    public function update_calendar(Proposal $proposal)
    {
        // dd("");
        // $this->proposal = $proposal;
        // $this->proposal->refresh();
        // $this->proposal->refresh();
        // $this->events;
        // $this->events = '';
        // $this->emit('refreshCalendar');
        // $this->mount();
        $this->render();
    }

    // public function mount(){
        
    // }

    public function render()
    {
        $array = array();
        $key = 0;

        $monday = now()->startOfWeek();
        $tuesday = now()->startOfWeek()->addDays(1);
        $wednesday = now()->startOfWeek()->addDays(2);
        $thursday = now()->startOfWeek()->addDays(3);
        $friday = now()->startOfWeek()->addDays(4);
        $saturday = now()->startOfWeek()->addDays(5);
        $sunday = now()->startOfWeek()->addDays(6);

        // $this->proposal->refresh();
        foreach($this->proposal->details->sortBy('day') as $detail){
            foreach (CarbonPeriod::create($detail->start_hour, $detail->duration . ' minutes', $detail->end_hour)->excludeEndDate() as $period) {
                if($detail->day == 1){$date = $monday->format('Y-m-d') . " " . $period->format('H:i');}
                if($detail->day == 2){$date = $tuesday->format('Y-m-d') . " " . $period->format('H:i');}
                if($detail->day == 3){$date = $wednesday->format('Y-m-d') . " " . $period->format('H:i');}
                if($detail->day == 4){$date = $thursday->format('Y-m-d') . " " . $period->format('H:i');}
                if($detail->day == 5){$date = $friday->format('Y-m-d') . " " . $period->format('H:i');}
                if($detail->day == 6){$date = $saturday->format('Y-m-d') . " " . $period->format('H:i');}
                if($detail->day == 7){$date = $sunday->format('Y-m-d') . " " . $period->format('H:i');}

                $array[$key]['title'] = $detail->activityType->name;
                $array[$key]['start'] = $date;
                $array[$key]['end'] = Carbon::parse($date)->addMinutes($detail->duration);
                $key +=1;
            }
        }

        // dd($array);
        $this->events = json_encode($array);
        // $this->emit('refreshCalendar');
        // $this->events = null;
        // $this->emit('refreshCalendar');

        // $this->clearCache();
        // $this->emit('refreshCalendar');

        return view('livewire.prof-agenda.proposal-calendar');
    }
}
