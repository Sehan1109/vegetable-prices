<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'market_id',
        'vegetable_id',
        'price_record_id',
        'date'
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function vegetable()
    {
        return $this->belongsTo(Vegetable::class);
    }

    public function priceRecord()
    {
        return $this->belongsTo(PriceRecord::class);
    }
}
