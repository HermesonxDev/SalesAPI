<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    
    protected $connection = 'mysql';
    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',    
        'order_id',
        'description',
        'value',
        'finished',
        'canceled',
        'deleted'
    ];

    public $timestamps = true;
}
