<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('transaction_date')
                    ->default(now())
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Checkbox::make('is_paid_self')
                    ->label('Bayar sendiri')
                    ->reactive()
                    ->afterStateHydrated(fn($set, $record) => $set('is_paid_self', filled($record?->paid_by)))
                    ->dehydrated(false)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('paid_by', $state ? auth()->id() : null);
                    }),

                Hidden::make('paid_by')
                    ->dehydrated(true)
                    ->nullable(),
            ]);
    }
}
