<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vegetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'slug',
    ];

    public function priceRecords()
    {
        return $this->hasMany(PriceRecord::class);
    }
}
