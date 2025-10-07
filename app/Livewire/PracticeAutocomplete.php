<?php

namespace App\Livewire;

use App\Models\Practice;
use Livewire\Component;

class PracticeAutocomplete extends Component
{
    public $search = '';
    public $selectedPracticeId = null;
    public $selectedPracticeName = '';
    public $showDropdown = false;

    public function mount($practiceId = null)
    {
        if ($practiceId) {
            $practice = Practice::find($practiceId);
            if ($practice) {
                $this->selectedPracticeId = $practice->id;
                $this->selectedPracticeName = $practice->name;
                $this->search = $practice->name;
            }
        }
    }

    public function updatedSearch()
    {
        $this->showDropdown = strlen($this->search) > 0;

        if (empty($this->search)) {
            $this->selectedPracticeId = null;
            $this->selectedPracticeName = '';
        }
    }

    public function selectPractice($practiceId, $practiceName)
    {
        $this->selectedPracticeId = $practiceId;
        $this->selectedPracticeName = $practiceName;
        $this->search = $practiceName;
        $this->showDropdown = false;

        $this->dispatch('practiceSelected', practiceId: $practiceId);
    }

    public function clearSelection()
    {
        $this->selectedPracticeId = null;
        $this->selectedPracticeName = '';
        $this->search = '';
        $this->showDropdown = false;

        $this->dispatch('practiceSelected', practiceId: null);
    }

    public function render()
    {
        $practices = [];

        if ($this->showDropdown && strlen($this->search) > 0) {
            $practices = Practice::where('name', 'like', "%{$this->search}%")
                ->orderBy('name')
                ->limit(10)
                ->get();
        }

        return view('livewire.practice-autocomplete', [
            'practices' => $practices,
        ]);
    }
}
