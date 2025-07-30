<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{

    public $totalRevenue;
    public $pendingInvoicesCount;
    public $overdueInvoicesCount;
    public $recentInvoices;
    public $totalClients;
    public $paidInvoicesCount;
    public $chartType = 'line';
    public $monthlyRevenueData = [];
    public $revenueStats = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Total revenue calculation
        $this->totalRevenue = Invoice::where('status', 'paid')->sum('total');

        // Pending invoices count
        $this->pendingInvoicesCount = Invoice::where('status', 'sent')->count();

        // Overdue invoices count
        $this->overdueInvoicesCount = Invoice::where('status', 'sent')
            ->where('due_date', '<', now())
            ->count();

        // Paid invoices count
        $this->paidInvoicesCount = Invoice::where('status', 'paid')->count();

        // Total clients
        $this->totalClients = Client::count();

        // Recent invoices (last 10)
        $this->recentInvoices = Invoice::with('client')
            ->latest()
            ->take(10)
            ->get()
            ->count();

        // Monthly revenue data for chart
        $this->monthlyRevenueData = $this->getMonthlyRevenueData();
    }

    private function getMonthlyRevenueData()
    {
        $monthlyData = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('YEAR(paid_at) as year'),
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $chartData = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $monthData = $monthlyData->where('year', $date->year)
                ->where('month', $date->month)
                ->first();

            $chartData[] = $monthData ? $monthData->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $chartData
        ];
    }

       public function getRevenueStats()
    {
        $currentMonth = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        $lastMonth = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('total');

        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $totalPaidInvoices = Invoice::where('status', 'paid')->count();

        $growth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return [
            'current_month' => (float) $currentMonth,
            'last_month' => (float) $lastMonth,
            'total_revenue' => (float) $totalRevenue,
            'total_paid_invoices' => $totalPaidInvoices,
            'growth_percentage' => round($growth, 2)
        ];
    }

    public function changeChartType($type)
    {
        $this->chartType = $type;
        $this->dispatch('chartTypeChanged', $type);
    }

    public function getAverageInvoiceValueProperty()
    {
        $totalInvoices = Invoice::where('status', '!=', 'draft')->count();
        return $totalInvoices > 0 ? $this->totalRevenue / $totalInvoices : 0;
    }

    public function getCollectionRateProperty()
    {
        $totalSent = Invoice::whereIn('status', ['sent', 'paid'])->sum('total');
        $totalPaid = Invoice::where('status', 'paid')->sum('total');

        return $totalSent > 0 ? ($totalPaid / $totalSent) * 100 : 0;
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('chartDataUpdated', $this->monthlyRevenueData);
        $this->dispatch('dashboard-updated');
    }

        public function refreshChartData()
    {
        // Reload all dashboard data
        $this->loadDashboardData();

        // Dispatch event to update the chart
        $this->dispatch('chartDataUpdated', $this->monthlyRevenueData);

        // Optional: Show success message
        session()->flash('message', 'Chart data refreshed successfully!');
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
