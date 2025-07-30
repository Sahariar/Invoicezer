// public/js/livewire-dashboard-charts.js

class LivewireDashboardCharts {
    constructor() {
        this.chart = null;
        this.colors = {
            primary: '#667eea',
            secondary: '#764ba2',
            success: '#059669',
            danger: '#dc2626',
            warning: '#d97706',
            info: '#0284c7'
        };
        this.init();
    }

    init() {
        // Wait for Livewire to be ready
        document.addEventListener('livewire:init', () => {
            this.setupEventListeners();
        });

        // Initialize on DOM ready if Livewire is already loaded
        if (window.Livewire) {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        // Listen for Livewire events
        Livewire.on('chartTypeChanged', (data) => {
            this.updateChartType(data[0]);
        });

        Livewire.on('chartDataUpdated', (data) => {
            this.updateChartData(data[0]);
        });

        // Listen for page navigation
        document.addEventListener('livewire:navigated', () => {
            this.reinitializeChart();
        });
    }

    initChart(canvasId, data, type = 'line') {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        // Destroy existing chart
        if (this.chart) {
            this.chart.destroy();
        }

        const config = {
            type: type === 'area' ? 'line' : type,
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: data.data,
                    borderColor: this.colors.primary,
                    backgroundColor: this.getBackgroundColor(type),
                    borderWidth: 3,
                    fill: type === 'area',
                    tension: 0.4,
                    pointBackgroundColor: this.colors.primary,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                }]
            },
            options: this.getChartOptions()
        };

        this.chart = new Chart(ctx, config);
        return this.chart;
    }

    getBackgroundColor(type) {
        switch(type) {
            case 'area':
                return 'rgba(102, 126, 234, 0.1)';
            case 'bar':
                return 'rgba(102, 126, 234, 0.8)';
            default:
                return 'transparent';
        }
    }

    getChartOptions() {
        return {
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
                    borderColor: this.colors.primary,
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
                        color: '#f3f4f6',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280',
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
                        color: '#6b7280'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
            }
        };
    }

    updateChartType(type) {
        if (!this.chart) return;

        const dataset = this.chart.data.datasets[0];

        // Update chart type
        this.chart.config.type = type === 'area' ? 'line' : type;

        // Update dataset properties
        dataset.backgroundColor = this.getBackgroundColor(type);
        dataset.fill = type === 'area';

        this.chart.update('active');
    }

    updateChartData(newData) {
        if (!this.chart) return;

        this.chart.data.labels = newData.labels;
        this.chart.data.datasets[0].data = newData.data;
        this.chart.update('active');
    }

    reinitializeChart() {
        // This method can be called when navigating between pages
        // if you're using Livewire's SPA mode
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
    }

    // Utility method to show loading state
    showChartLoading(canvasId) {
        const canvas = document.getElementById(canvasId);
        const container = canvas?.parentElement;

        if (container && !container.querySelector('.chart-loading')) {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chart-loading';
            loadingDiv.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading chart data...</span>
                </div>
            `;
            container.appendChild(loadingDiv);
        }
    }

    hideChartLoading(canvasId) {
        const canvas = document.getElementById(canvasId);
        const container = canvas?.parentElement;
        const loading = container?.querySelector('.chart-loading');

        if (loading) {
            loading.remove();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.dashboardCharts = new LivewireDashboardCharts();
});

// Additional CSS for loading state (add to your main CSS file)
const chartLoadingCSS = `
    .chart-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        color: #6b7280;
    }

    .loading-spinner i {
        font-size: 2rem;
        color: #667eea;
    }

    .loading-spinner span {
        font-weight: 500;
    }
`;

// Inject CSS if not already present
if (!document.querySelector('#chart-loading-styles')) {
    const style = document.createElement('style');
    style.id = 'chart-loading-styles';
    style.textContent = chartLoadingCSS;
    document.head.appendChild(style);
}
