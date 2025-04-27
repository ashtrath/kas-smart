<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Number;

class SellingOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $todayStart = today()->startOfDay();
        $todayEnd = today()->endOfDay();

        $todayStats = Sale::query()
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw('SUM(total) as total_revenue, COUNT(*) as total_sales, AVG(total) as average_order_value')
            ->first();

        $todayRevenue = $todayStats->total_revenue ?? 0;
        $todaySalesCount = $todayStats->total_sales ?? 0;
        $todayAverageOrderValue = $todaySalesCount > 0 ? ($todayStats->average_order_value ?? 0) : 0;

        $yesterdayStart = today()->subDay()->startOfDay();
        $yesterdayEnd = today()->subDay()->endOfDay();

        $yesterdayRevenue = Sale::query()
            ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
            ->sum('total') ?? 0;

        $revenueDifference = $todayRevenue - $yesterdayRevenue;

        $chartStartDate = today()->subDays(6)->startOfDay();
        $dailyRevenueData = Sale::query()
            ->whereBetween('created_at', [$chartStartDate, $todayEnd])
            ->selectRaw("strftime('%Y-%m-%d', created_at) as sale_date, SUM(total) as daily_total")
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->pluck('daily_total', 'sale_date');

        $chartData = [];
        $loopDate = $chartStartDate->copy();
        while ($loopDate <= $todayEnd) {
            $dateKey = $loopDate->format('Y-m-d');
            $chartData[] = $dailyRevenueData->get($dateKey, 0);
            $loopDate->addDay();
        }

        return [
            Stat::make('Pendapatan Hari Ini', $this->formatCurrency($todayRevenue))
                ->description($revenueDifference != 0 ?
                    sprintf(
                        '%s %s',
                        $this->formatCurrency(abs($revenueDifference)),
                        $revenueDifference > 0 ? 'naik' : 'turun'
                    ) : 'Tidak ada perubahan')
                ->descriptionIcon($revenueDifference === 0 ? null : ($revenueDifference > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down'))
                ->color($revenueDifference === 0 ? 'secondary' : ($revenueDifference > 0 ? 'success' : 'danger'))
                ->chart($chartData),

            Stat::make('Jumlah Penjualan Hari Ini', $this->formatNumber($todaySalesCount))
                ->description('Total transaksi hari ini')
                ->color('primary'), // No chart or comparison added here for brevity

            Stat::make('Rata-Rata Pesanan Hari Ini', $this->formatCurrency($todayAverageOrderValue))
                ->description('Nilai rata-rata transaksi hari ini')
                ->color('info'), // No chart or comparison added here for brevity
        ];
    }

    private function formatNumber(float|int|null $number): string
    {
        if (is_null($number)) {
            return '0';
        }
        $number = (float) $number;

        if ($number < 1000) {
            return Number::format($number, precision: ($number === (int) $number) ? 0 : 2, locale: 'id');
        }
        if ($number < 1000000) {
            return Number::format($number / 1000, precision: 2, locale: 'id').'k';
        }

        return Number::format($number / 1000000, precision: 2, locale: 'id').'jt';
    }

    private function formatCurrency(float|int|null $number): string
    {
        if (is_null($number)) {
            return Number::currency(0, 'IDR', 'id');
        }
        if (abs($number) >= 1000) {
            return 'Rp '.$this->formatNumber($number);
        }

        return Number::currency($number, 'IDR', 'id');
    }
}
