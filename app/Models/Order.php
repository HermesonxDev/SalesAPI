<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    
    protected $connection = 'mysql';
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_quantity',
        'description',
        'value',
        'finished',
        'canceled',
        'deleted'
    ];

    public $timestamps = true;
}
