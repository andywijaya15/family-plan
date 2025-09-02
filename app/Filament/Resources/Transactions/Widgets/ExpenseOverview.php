<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Filament\Resources\Transactions\Pages\ListTransactions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpenseOverview extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery()
            ->selectRaw('paid_by, SUM(amount) as total')
            ->whereNotNull('paid_by')
            ->groupBy('paid_by')
            ->with('paidBy');

        $query->getQuery()->orders = [];

        $transactions = $query->get();

        $stats = [];

        foreach ($transactions as $tx) {
            $paidByName = $tx->paidBy->name ?? 'Unknown';

            $stats[] = Stat::make(
                "{$paidByName}",
                'Rp ' . number_format($tx->total, 0, ',', '.')
            )
                ->description('Total Reimbursed Bulan Ini')
                ->color('danger');
        }

        return $stats;
    }
}
