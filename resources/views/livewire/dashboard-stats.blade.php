<div class="grid auto-rows-min gap-4 md:grid-cols-3">
    {{-- Total Revenue Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Revenue</h3>
            <p class="text-3xl font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    {{-- Pending Invoices Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Pending Invoices</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $pendingInvoicesCount }}</p>
        </div>
    </div>

    {{-- Collection Rate Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Collection Rate</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($collectionRate, 1) }}%</p>
        </div>
    </div>

</div>
