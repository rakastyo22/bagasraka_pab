<?php

namespace App\Models;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
        use HasFactory;
        protected $fillable = [
        'city_id',
        'province_id',
        'province',
        'type',
        'city_name',
        'postal_code',
    ];
}
