<?php

namespace App\Livewire;

use App\LeadSource;
use App\LeadStatus;
use App\Models\Lead;
use Livewire\Component;
use Livewire\WithPagination;

class LeadsDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sourceFilter = '';
    public $practiceFilter = null;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $selectedLead = null;
    public $showModal = false;

    public $editingStatus = '';
    public $editingContactedAt = '';
    public $showEmailLogs = false;

    protected $queryString = ['search', 'statusFilter', 'sourceFilter', 'practiceFilter'];

    protected $listeners = ['practiceSelected'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSourceFilter()
    {
        $this->resetPage();
    }

    public function practiceSelected($practiceId)
    {
        $this->practiceFilter = $practiceId;
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function viewLead($leadId)
    {
        $this->selectedLead = Lead::with(['practice', 'emailLogs'])->find($leadId);
        $this->editingStatus = $this->selectedLead->status->value;
        $this->editingContactedAt = $this->selectedLead->contacted_at?->format('Y-m-d\TH:i');
        $this->showEmailLogs = false;
        $this->showModal = true;
    }

    public function updateLead()
    {
        $this->validate([
            'editingStatus' => 'required|in:new,contacted,qualified,converted,rejected',
            'editingContactedAt' => 'nullable|date',
        ]);

        $this->selectedLead->update([
            'status' => LeadStatus::from($this->editingStatus),
            'contacted_at' => $this->editingContactedAt,
        ]);

        $this->showModal = false;
        $this->selectedLead = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedLead = null;
    }

    public function deleteLead($leadId)
    {
        Lead::find($leadId)->delete();
    }

    public function resendNotification()
    {
        if (!$this->selectedLead) {
            return;
        }

        event(new \App\Events\LeadCreated($this->selectedLead));

        $this->selectedLead->refresh();
        $this->selectedLead->load('emailLogs');
    }

    public function markAsContacted()
    {
        if (!$this->selectedLead) {
            return;
        }

        $this->editingContactedAt = now()->format('Y-m-d\TH:i');
        $this->editingStatus = LeadStatus::CONTACTED->value;
    }

    public function render()
    {
        $query = Lead::with(['practice', 'practice.salesReps']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->sourceFilter) {
            $query->where('source', $this->sourceFilter);
        }

        if ($this->practiceFilter) {
            $query->where('practice_id', $this->practiceFilter);
        }

        $leads = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.leads-dashboard', [
            'leads' => $leads,
            'statuses' => LeadStatus::cases(),
            'sources' => LeadSource::cases(),
        ]);
    }
}
