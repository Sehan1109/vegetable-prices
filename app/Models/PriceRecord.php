<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRecord extends Model
{
    use HasFactory;

    protected $table = 'price_records';

    protected $fillable = [
        'date',
        'market_id',
        'vegetable_id',
        'price',
        'price_yesterday',
        'change_percent',
        'trend',
        'price_min',
        'price_max',
        'price_average'
    ];

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function market()
    {
        return $this->belongsTo(Market::class, 'market_id', 'slug');
    }

    public function vegetable()
    {
        return $this->belongsTo(Vegetable::class, 'vegetable_id', 'slug');
    }
}
