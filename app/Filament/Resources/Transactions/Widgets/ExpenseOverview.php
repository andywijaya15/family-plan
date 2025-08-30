<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ExpenseOverview extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        $transactions = $this->getPageTableQuery()
            ->whereNotNull('paid_by')
            ->get(['paid_by', 'amount']);

        $totals = [];

        foreach ($transactions as $tx) {
            $id = $tx->paid_by;

            if (!isset($totals[$id])) {
                $totals[$id] = 0;
            }

            $totals[$id] += $tx->amount;
        }

        $stats = [];

        foreach ($totals as $paidById => $total) {
            $paidByName = $transactions->firstWhere('paid_by', $paidById)->paidBy->name ?? 'Unknown';

            $stats[] = Stat::make("Paid by: {$paidByName}", 'Rp ' . number_format($total, 0, ',', '.'))
                ->description('Total expense')
                ->color('danger');
        }

        return $stats;
    }
}
