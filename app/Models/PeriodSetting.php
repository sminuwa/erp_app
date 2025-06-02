<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodSetting extends Model
{
    // Optional: If you're not using the default 'id' as primary key or want to customize
    protected $table = 'period_settings';

    protected $fillable = [
        'period_open',
        'period_close',
    ];

    // Optional: Cast dates as Carbon instances
    protected $casts = [
        'period_open' => 'date',
        'period_close' => 'date',
    ];
}
