<?php

namespace App\Models;

use App\Models\User;
use App\Models\Alamat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alamat extends Model
{
        use HasFactory;
        protected $fillable = [
        'user_id',
        'alamat',
        'province_id',
        'kota_id',
    ];
        public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
        }
        
        public function city(){
        return $this->hasOne(City::class, 'city_id', 'kota_id');
        }
}
