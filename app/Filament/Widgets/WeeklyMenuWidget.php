<?php

namespace App\Filament\Widgets;

use Exception;
use App\Models\Menu;
use App\Models\DailyMenu;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Filament\Widgets\TableWidget;
use Filament\Support\Colors\Color;
use Filament\Tables\Filters\Filter;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;

class WeeklyMenuWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->deferLoading(true)
            ->query(fn(): Builder => DailyMenu::query()
                ->with('menu'))
            ->columns([
                TextColumn::make('menu_date')
                    ->date('l, F d, Y'),
                TextColumn::make('menu.name'),
            ])
            ->filters([
                Filter::make('week')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start of Week')
                            ->default(now()->startOfWeek(Carbon::MONDAY)),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! $data['start_date']) {
                            return $query;
                        }

                        $start = Carbon::parse($data['start_date'])->startOfWeek(Carbon::MONDAY);
                        $end   = Carbon::parse($data['start_date'])->endOfWeek(Carbon::SUNDAY);

                        return $query->whereBetween('menu_date', [$start, $end]);
                    })
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                Action::make('Randomize Menu for Next Week')
                    ->action(function () {
                        try {
                            self::generateWorkweekMealPlan();

                            Notification::make()
                                ->title('Success')
                                ->body('Meal plan generated successfully.')
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(function () {
                        return now()->isWeekend();
                    })
                    ->requiresConfirmation()
                    ->label('Randomize Menu for Next Week')
                    ->icon(Heroicon::ArrowPath)
                    ->color(Color::Green),
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     //
                // ]),
            ]);
    }

    public static function generateWorkweekMealPlan()
    {
        $startDate = now()->next(Carbon::MONDAY);
        $endDate = $startDate->copy()->addDays(4);

        DailyMenu::query()
            ->whereBetween('menu_date', [$startDate, $endDate])
            ->delete();

        $menus = Menu::query()
            ->pluck('id')
            ->shuffle()
            ->toArray();

        if (count($menus) < 5) {
            throw new Exception('Not enough unique menu items for the meal plan.');
        }

        foreach (range(0, 4) as $i) {
            DailyMenu::create([
                'menu_date' => $startDate->copy()->addDays($i),
                'menu_id' => $menus[$i],
            ]);
        }
    }
}
