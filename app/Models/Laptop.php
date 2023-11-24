<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'CPU',
        'GPU',
        'ram',
        'storage',
        'screen',
        'price',
        'description',
    ];

    public function images()
    {
        return $this->hasMany(LaptopImage::class);
    }
}
