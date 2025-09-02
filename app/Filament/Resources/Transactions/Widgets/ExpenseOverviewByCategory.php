<?php

namespace App\Filament\Resources\Transactions\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\Transactions\Pages\ListTransactions;

class ExpenseOverviewByCategory extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery()
            ->selectRaw('category_id, SUM(amount) as total')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->with('category');

        $query->getQuery()->orders = [];

        $transactions = $query->orderByDesc('total')->get();

        $stats = [];

        foreach ($transactions as $tx) {
            $categoryName = $tx->category->name ?? 'Unknown';

            $stats[] = Stat::make(
                "{$categoryName}",
                'Rp ' . number_format($tx->total, 0, ',', '.')
            )
                ->description('Total Pengeluaran Bulan Ini')
                ->color('danger');
        }

        return $stats;
    }
}
