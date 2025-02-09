<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Order extends Model
{
    use AsSource, HasFactory;

    protected $fillable = [
        'phone',
        'vin',
        'status',
    ];
}
