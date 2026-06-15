<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'price_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'market_id',
        'vegetable_id',
        'price',
        'price_yesterday',
        'change_percent',
        'trend',
    ];

    /**
     * Scope a query to only include prices for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
