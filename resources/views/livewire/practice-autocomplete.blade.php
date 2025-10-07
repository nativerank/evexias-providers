<div class="relative z-10" x-data="{ open: @entangle('showDropdown') }" @click.away="open = false">
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by practice..."
            @focus="open = true"
            class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-3 pe-3 bg-white dark:bg-white/10 dark:disabled:bg-white/[7%] text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500 shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5"
        />

        @if($selectedPracticeId)
            <button
                type="button"
                wire:click="clearSelection"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    @if($showDropdown && count($practices) > 0)
        <div class="absolute z-50 w-full mt-2 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-xl max-h-80 overflow-auto">
            @foreach($practices as $practice)
                <button
                    type="button"
                    wire:click="selectPractice({{ $practice->id }}, '{{ addslashes($practice->name) }}')"
                    class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:bg-gray-50 dark:focus:bg-zinc-700 focus:outline-none border-b border-gray-100 dark:border-zinc-700 last:border-0"
                >
                    <div class="font-medium text-base text-gray-900 dark:text-white">{{ $practice->name }}</div>
                    @if($practice->address)
                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">{{ $practice->address }}</div>
                    @endif
                </button>
            @endforeach
        </div>
    @endif

    @if($showDropdown && strlen($search) > 0 && count($practices) === 0)
        <div class="absolute z-50 w-full mt-2 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-xl">
            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                No practices found
            </div>
        </div>
    @endif
</div>
