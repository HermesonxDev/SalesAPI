<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefusedOrder extends Model {
    
    protected $connection = 'mysql';
    protected $table = 'refused_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'order_id',
        'reason',
        'canceled',
        'deleted'
    ];

    public $timestamps = true;
}
