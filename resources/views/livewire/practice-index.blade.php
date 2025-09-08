<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use function Livewire\Volt\{title};


new class extends Component {
    use WithPagination;

    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function practices(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return \App\Models\Practice::query()
            ->with(['practitioners'])
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }



};

title('Practices');
?>


<flux:table :paginate="$this->practices">
    <flux:table.columns>
        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Prctice Name</flux:table.column>

        <flux:table.column>Phone</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                           wire:click="sort('status')">Status
        </flux:table.column>
        <flux:table.column>Address
        </flux:table.column>
        <flux:table.column>External ID</flux:table.column>
        <flux:table.column>Practitioners</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">
            Created At
        </flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach ($this->practices as $practice)
            <flux:table.row :key="$practice->id">
                <flux:table.cell class="flex items-center gap-3">

                    {{ $practice->name }}
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $practice->phone }}</flux:table.cell>

                <flux:table.cell>
                    <flux:badge size="sm" color="green"
                                inset="top bottom">{{ $practice->status }}</flux:badge>
                </flux:table.cell>

                <flux:table.cell><spam class="truncate block text-ellipsis w-[100px]">{{ $practice->address }}</spam></flux:table.cell>
                <flux:table.cell>{{ $practice->external_id }}</flux:table.cell>

                <flux:table.cell class="whitespace-nowrap"> <flux:badge size="sm"
                                                                        inset="top bottom">{{ $practice->practitioners->count() }}</flux:badge></flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $practice->created_at->setTimezone('America/Denver')->format('M j, Y h:s A') }}</flux:table.cell>

            </flux:table.row>

        @endforeach
    </flux:table.rows>
</flux:table>

