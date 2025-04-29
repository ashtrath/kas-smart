<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Penjualan per Bulan';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();

        $salesData = Sale::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, COUNT(*) as sales_count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('sales_count', 'month')
            ->toArray();

        $monthlySales = [];
        $monthLabels = [];
        $months = collect(range(1, 12));

        $months->each(function ($month) use ($salesData, &$monthlySales, &$monthLabels) {
            $monthLabels[] = Carbon::create()?->month($month)->format('M');
            $monthlySales[] = $salesData[$month] ?? 0;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'data' => $monthlySales,
                    'fill' => 'start',
                ],
            ],
            'labels' => $monthLabels,
        ];
    }
}
