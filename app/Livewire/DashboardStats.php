<?php

namespace App\Livewire;

use App\LeadStatus;
use App\Models\Lead;
use App\Models\Practice;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        $totalPractices = Practice::count();
        $totalLeads = Lead::count();

        $newLeads7Days = Lead::where('status', LeadStatus::NEW)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $newLeads30Days = Lead::where('status', LeadStatus::NEW)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $contactedLeads7Days = Lead::where('status', LeadStatus::CONTACTED)
            ->where('contacted_at', '>=', now()->subDays(7))
            ->count();

        $contactedLeads30Days = Lead::where('status', LeadStatus::CONTACTED)
            ->where('contacted_at', '>=', now()->subDays(30))
            ->count();

        $totalContactedLeads = Lead::where('status', LeadStatus::CONTACTED)
            ->count();

        $totalNotContactedLeads = Lead::where('status', '!=', LeadStatus::CONTACTED)
            ->count();

        return view('livewire.dashboard-stats', [
            'totalPractices' => $totalPractices,
            'totalLeads' => $totalLeads,
            'newLeads7Days' => $newLeads7Days,
            'newLeads30Days' => $newLeads30Days,
            'contactedLeads7Days' => $contactedLeads7Days,
            'contactedLeads30Days' => $contactedLeads30Days,
            'totalContactedLeads' => $totalContactedLeads,
            'totalNotContactedLeads' => $totalNotContactedLeads,
        ]);
    }
}
