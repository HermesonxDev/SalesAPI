<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    
    protected $connection = 'mysql';
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'active',
        'deleted'
    ];

    public $timestamps = true;
}
