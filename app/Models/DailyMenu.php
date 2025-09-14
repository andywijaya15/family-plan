<?php

namespace App\Models;

use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMenu extends Model
{
    use HasAudit, SoftDeletes;

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
