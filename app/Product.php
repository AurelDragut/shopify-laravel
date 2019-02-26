<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'IdType', 'barcode', 'sku', 'price', 'inventory','response','updated'
    ];
}
