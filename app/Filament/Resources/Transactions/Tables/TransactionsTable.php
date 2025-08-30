<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->hidden()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR', decimalPlaces: 0)
                    ->sortable()
                    ->summarize([
                        Sum::make()->label('Total')
                            ->money('IDR', decimalPlaces: 0),
                    ]),
                TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('paidBy.name')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])
                            ->default(now()->month),
                        Select::make('year')
                            ->label('Tahun')
                            ->options(
                                collect(range(now()->year - 5, now()->year + 1))
                                    ->mapWithKeys(fn ($y) => [$y => $y])
                            )
                            ->default(now()->year),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['month'], fn ($q, $month) => $q->whereMonth('transaction_date', $month))
                            ->when($data['year'], fn ($q, $year) => $q->whereYear('transaction_date', $year));
                    }),
            ], layout: FiltersLayout::Modal)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
