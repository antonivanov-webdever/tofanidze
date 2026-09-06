<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'type',
        'path',
        'referrer',
    ];
}
