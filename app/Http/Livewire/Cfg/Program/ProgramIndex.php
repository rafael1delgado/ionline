<?php

namespace App\Http\Livewire\Cfg\Program;

use App\Models\Cfg\Program;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;

    public function render()
    {
        return view('livewire.cfg.program.program-index', [
            'programs' => $this->getPrograms()
        ]);
    }

    public function getPrograms()
    {
        $search = "%$this->search%";

        $programs = Program::query()
            ->when($this->search, function ($query) use ($search) {
                $query->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            })
            ->orderBy('name')
            ->paginate(10);

        return $programs;
    }

    public function delete(Program $program)
    {
        $program->delete();
        $this->render();
    }
}
