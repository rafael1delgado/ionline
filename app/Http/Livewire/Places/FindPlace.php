<?php

namespace App\Http\Livewire\Places;

use App\Models\Establishment;
use App\Models\Parameters\Place;
use Livewire\Component;

class FindPlace extends Component
{
    public $establishment;

    public $places;
    public $place_id;
    public $search;
    public $tagId;
    public $showResult;
    public $smallInput = false;
    public $placeholder;

    protected $listeners = [
        'clearSearchPlace' => 'clearSearch',
    ];

    public function mount(Establishment $establishment)
    {
        $this->places = collect([]);
    }

    public function render()
    {
        return view('livewire.places.find-place');
    }

    public function updatedSearch()
    {
        $this->showResult = true;
        $this->places = collect([]);
        $search = "%" . $this->search . "%";

        if($this->search)
        {
            $this->places = Place::query()
                ->whereEstablishmentId($this->establishment->id)
                ->where(function($query) use($search) {
                    $query->where('name', 'like', $search)
                    ->orWhereHas('location', function($query) use($search) {
                        $query->where('name', 'like', $search)
                            ->whereEstablishmentId($this->establishment->id);
                    });
                })
                ->limit(5)
                ->get();
        }
    }

    public function addSearchPlace(Place $place)
    {
        $this->showResult = false;
        $this->search = $place->id. ",". $place->name . ", " . $place->location->name;
        $this->place_id = $place->id;
        $this->places = collect([]);

        $this->emit('myPlaceId', $this->place_id);
    }

    public function clearSearch()
    {
        $this->emit('myPlaceId', null);

        $this->showResult = false;
        $this->places = collect([]);
        $this->place_id = null;
        $this->search = null;
    }
}
