<div>
    <div class="mb-6 space-y-4">
        <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center">
            <div class="flex-none w-full md:w-sm">
                <flux:input wire:model.live.debounce.300ms="search" autocomplete="off" placeholder="Search leads..."
                            class=""/>
            </div>
            <div class="flex-none w-full md:w-sm">
                @livewire('practice-autocomplete', ['practiceId' => $practiceFilter])
            </div>
            <div class="w-full md:w-40">
                <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-full md:w-40">
                <flux:select wire:model.live="sourceFilter" placeholder="All Sources">
                    <option value="">All Sources</option>
                    @foreach($sources as $source)
                        <option value="{{ $source->value }}">{{ ucfirst($source->value) }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <flux:table :paginate="$leads">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'lead_type'" :direction="$sortDirection"
                                   wire:click="sort('lead_type')">Lead Type
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'first_name'" :direction="$sortDirection"
                                   wire:click="sort('first_name')">Name
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection"
                                   wire:click="sort('email')">Email
                </flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'practice_id'" :direction="$sortDirection"
                                   wire:click="sort('practice_id')">Practice
                </flux:table.column>
                <flux:table.column>Sales Rep</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                                   wire:click="sort('status')">Status
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'source'" :direction="$sortDirection"
                                   wire:click="sort('source')">Source
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'lead_created_at'" :direction="$sortDirection"
                                   wire:click="sort('lead_created_at')">Lead Date
                </flux:table.column>
                <flux:table.column class="sticky right-0 bg-white dark:bg-zinc-900">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($leads as $lead)
                    <flux:table.row :key="$lead->id">
                        <flux:table.cell>
                            @if($lead->lead_type)
                                <flux:badge size="sm" color="zinc" inset="top bottom">
                                    {{ $lead->lead_type }}
                                </flux:badge>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $lead->first_name }} {{ $lead->last_name }}</flux:table.cell>
                        <flux:table.cell>{{ $lead->email }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">{{ $lead->phone }}</flux:table.cell>
                        <flux:table.cell>{{ $lead->practice->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if($lead->practice->salesReps->isNotEmpty())
                                {{ $lead->practice->salesReps->pluck('name')->join(', ') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                :color="match($lead->status->value) {
                                'new' => 'blue',
                                'contacted' => 'yellow',
                                'qualified' => 'purple',
                                'converted' => 'green',
                                'rejected' => 'red',
                                default => 'gray'
                            }"
                                inset="top bottom">
                                {{ ucfirst($lead->status->value) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="gray" inset="top bottom">
                                {{ ucfirst($lead->source?->value ?? 'unknown') }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">
                            {{ $lead->lead_created_at?->setTimezone('America/Denver')->format('M j, Y h:i A') }}
                        </flux:table.cell>
                        <flux:table.cell class="sticky right-0 bg-white dark:bg-zinc-900">
                            <div class="flex gap-2 pr-2">
                                <flux:button size="sm" wire:click="viewLead({{ $lead->id }})">View</flux:button>
                                <flux:button size="sm" variant="danger" wire:click="deleteLead({{ $lead->id }})"
                                             wire:confirm="Are you sure you want to delete this lead?">Delete
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    @if($showModal && $selectedLead)
        <flux:modal name="lead-modal" wire:model="showModal" class="max-w-2xl">
            <form wire:submit="updateLead" class="space-y-6">
                <div>
                    <flux:heading size="lg">Lead Details
                        - {{ $selectedLead->first_name }} {{ $selectedLead->last_name }}</flux:heading>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>Practice</flux:label>
                            <p class="text-sm">{{ $selectedLead->practice->name }}</p>
                        </div>
                        <div>
                            <flux:label>Sales Rep</flux:label>
                            <p class="text-sm">
                                @if($selectedLead->practice->salesReps->isNotEmpty())
                                    {{ $selectedLead->practice->salesReps->pluck('name')->join(', ') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>First Name</flux:label>
                            <p class="text-sm">{{ $selectedLead->first_name }}</p>
                        </div>
                        <div>
                            <flux:label>Last Name</flux:label>
                            <p class="text-sm">{{ $selectedLead->last_name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>Email</flux:label>
                            <p class="text-sm">{{ $selectedLead->email }}</p>
                        </div>
                        <div>
                            <flux:label>Phone</flux:label>
                            <p class="text-sm">{{ $selectedLead->phone }}</p>
                        </div>
                    </div>

                    @if($selectedLead->data)
                        <div>
                            <hr class="mb-4">
                            <flux:label>Additional Information</flux:label>

                            <div
                                class="mt-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-zinc-700 rounded">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($selectedLead->data as $key => $value)
                                        @if(!in_array($key, ['first_name', 'firstName', 'fname', 'last_name', 'lastName', 'lname', 'email', 'email_address', 'emailAddress', 'phone', 'phone_number', 'phoneNumber', 'mobile', 'created_at', 'createdAt', 'lead_created_at', 'lead_type', 'leadType', 'type']))
                                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                                <td class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-1/2">
                                                    {{ ucwords(str_replace(['_', '-'], ' ', $key)) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                    @if(is_array($value))
                                                        {{ implode(', ', $value) }}
                                                    @elseif(is_bool($value))
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-4">
                            <flux:label>Lead Management</flux:label>
                            @if($selectedLead->status->value === 'new')
                                <flux:button wire:click="markAsContacted" variant="outline" size="sm" type="button">
                                    Mark as Contacted Now
                                </flux:button>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:label>Status</flux:label>
                                <flux:select wire:model="editingStatus" class="w-full">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <div>
                                <flux:label>Contacted At</flux:label>
                                <flux:input type="datetime-local" wire:model="editingContactedAt" class="w-full"/>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <flux:label>Source</flux:label>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($selectedLead->source?->value ?? 'unknown') }}</p>
                            </div>

                            <div>
                                <flux:label>Lead Type</flux:label>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedLead->lead_type ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <flux:label>Lead Created At</flux:label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedLead->lead_created_at?->setTimezone('America/Denver')->format('M j, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                @if($selectedLead->emailLogs->isNotEmpty())
                    <div class="pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <button
                            type="button"
                            wire:click="$toggle('showEmailLogs')"
                            class="flex items-center justify-between w-full text-left"
                        >
                            <flux:label>Email Delivery Logs ({{ $selectedLead->emailLogs->count() }})</flux:label>
                            <svg class="w-5 h-5 transition-transform {{ $showEmailLogs ? 'rotate-180' : '' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        @if($showEmailLogs)
                            <div class="mt-3 space-y-2 max-h-48 overflow-y-auto">
                                @foreach($selectedLead->emailLogs as $log)
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-zinc-800 rounded border border-gray-200 dark:border-zinc-700">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium">{{ $log->recipient_email }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $log->created_at->setTimezone('America/Denver')->format('M j, Y h:i A') }}
                                                </div>
                                            </div>
                                            <div>
                                                <flux:badge
                                                    size="sm"
                                                    :color="match($log->status->value) {
                                                        'sent' => 'green',
                                                        'pending' => 'blue',
                                                        'failed' => 'red',
                                                        'bounced' => 'orange',
                                                        default => 'gray'
                                                    }"
                                                    inset="top bottom">
                                                    {{ ucfirst($log->status->value) }}
                                                </flux:badge>
                                            </div>
                                        </div>
                                        @if($log->error_message)
                                            <div class="mt-2 text-xs text-red-600 dark:text-red-400">
                                                {{ $log->error_message }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center">
                    <flux:button wire:click="resendNotification" variant="outline" type="button">
                        Resend Email Notification
                    </flux:button>
                    <div class="flex space-x-2 rtl:space-x-reverse">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit">Update Lead</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
