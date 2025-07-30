{{-- resources/views/dashboard.blade.php (or wherever your main dashboard view is) --}}
<div>
    <div class="grid auto-rows-min gap-4 md:grid-cols-4">
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
    {{-- Overdue Invoices Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Overdue Invoices</h3>
            <p class="text-3xl font-bold text-orange-300">{{ $overdueInvoicesCount }}</p>
        </div>
    </div>
    {{-- Recent Invoices Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Recent Invoices</h3>
            <p class="text-3xl font-bold text-amber-300">{{ $recentInvoices }}</p>
        </div>
    </div>
    {{-- Paid Invoices Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Paid Invoices</h3>
            <p class="text-3xl font-bold text-teal-300">{{ $paidInvoicesCount }}</p>
        </div>
    </div>
    {{-- Total Clients Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Clients</h3>
            <p class="text-3xl font-bold text-teal-300">{{ $totalClients }}</p>
        </div>
    </div>

    {{-- Collection Rate Card --}}
    <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="stat-card p-6 h-full flex flex-col justify-center">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Collection Rate</h3>
            <p class="text-3xl font-bold text-sky-600">{{ number_format($this->collectionRate, 1) }}%</p>
        </div>
    </div>
</div>
    <!-- Revenue Chart -->
    <div class="chart-section">
        <div class="section-header">
            <h2>Monthly Revenue Trend</h2>
            <div class="chart-controls">
                <button wire:click="refreshChartData" class="btn btn-outline">
                    <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="refreshChartData"></i>
                    <span wire:loading.remove wire:target="refreshChartData">Refresh</span>
                    <span wire:loading wire:target="refreshChartData">Loading...</span>
                </button>
                <select wire:change="changeChartType($event.target.value)" class="form-select">
                    <option value="line" {{ $chartType === 'line' ? 'selected' : '' }}>Line Chart</option>
                    <option value="bar" {{ $chartType === 'bar' ? 'selected' : '' }}>Bar Chart</option>
                    <option value="area" {{ $chartType === 'area' ? 'selected' : '' }}>Area Chart</option>
                </select>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    let chart;
    let chartData = @json($monthlyRevenueData);

    function initChart(type = 'line') {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        const config = {
            type: type === 'area' ? 'line' : type,
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: chartData.data,
                    borderColor: '#00a63e',
                    backgroundColor: type === 'area' ? 'rgba(255, 255, 255, 0.1)' :
                    type === 'bar' ? 'rgba(255, 255, 255, 0.7)' : 'transparent',
                    borderWidth: 3,
                    fill: type === 'area',
                    tension: 0.4,
                    pointBackgroundColor: '#00a63e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#00a63e',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Revenue: ${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f2f2f2',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#f3f3f3',
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        };

        chart = new Chart(ctx, config);
    }

    // Initialize chart
    initChart('{{ $chartType }}');

    // Listen for Livewire events
    Livewire.on('chartTypeChanged', (type) => {
        initChart(type[0]);
    });

    Livewire.on('chartDataUpdated', (newData) => {
        chartData = newData[0];
        const currentType = '{{ $chartType }}';
        initChart(currentType);
    });
});
</script>
</div>
</div>


