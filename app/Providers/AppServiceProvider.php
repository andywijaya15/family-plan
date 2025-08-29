<?php

namespace App\Providers;

use Filament\Forms\Components\Field;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Column::configureUsing(function (Column $column): void {
            $label = $column->getLabel() ?? (string) Str::of($column->getName())
                ->afterLast('.')
                ->snake(' ')
                ->title();

            $column->label(strtoupper($label));
        });
        Field::configureUsing(function (Field $field): void {
            $label = $field->getLabel()
                ?? (string) Str::of($field->getName())
                    ->afterLast('.')
                    ->snake(' ')
                    ->title();

            $field->label(strtoupper($label));
        });
    }
}
