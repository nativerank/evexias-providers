<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Practices</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalPractices) }}</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Leads</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalLeads) }}</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Contacted Leads</div>
                <div class="text-3xl font-bold text-green-600 dark:text-green-500">{{ number_format($totalContactedLeads) }}</div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Not Contacted</div>
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-500">{{ number_format($totalNotContactedLeads) }}</div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Leads</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Last 7 Days</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ number_format($newLeads7Days) }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Last 30 Days</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ number_format($newLeads30Days) }}</div>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contacted Leads</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Last 7 Days</div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-500">{{ number_format($contactedLeads7Days) }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Last 30 Days</div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-500">{{ number_format($contactedLeads30Days) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
