<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'district',
        'province',
        'slug',
    ];

    public function priceRecords()
    {
        return $this->hasMany(PriceRecord::class);
    }
}
