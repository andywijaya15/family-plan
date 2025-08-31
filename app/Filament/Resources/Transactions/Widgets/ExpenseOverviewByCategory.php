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
        $transactions = $this->getPageTableQuery()
            ->whereNotNull('category_id') // pastikan field kategori
            ->get(['category_id', 'amount']);

        $totals = [];

        // Hitung total per kategori
        foreach ($transactions as $tx) {
            $id = $tx->category_id;

            if (!isset($totals[$id])) {
                $totals[$id] = 0;
            }

            $totals[$id] += $tx->amount;
        }

        $stats = [];

        foreach ($totals as $categoryId => $total) {
            $categoryName = $transactions->firstWhere('category_id', $categoryId)->category->name ?? 'Unknown';

            $stats[] = Stat::make("{$categoryName}", 'Rp ' . number_format($total, 0, ',', '.'))
                ->description('Total Pengeluaran Bulan Ini')
                ->color('danger');
        }

        return $stats;
    }
}
