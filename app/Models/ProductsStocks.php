<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsStocks extends Model
{
    protected $fillable = ['product_id', 'quantity'];

    use HasFactory;
}
