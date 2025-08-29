<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategoryType: string implements HasLabel
{
    case INCOME = 'INCOME';
    case EXPENSE = 'EXPENSE';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
