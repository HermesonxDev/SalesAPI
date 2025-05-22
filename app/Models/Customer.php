<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    
    protected $connection = 'mysql';
    protected $table = 'customers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'first_name',
        'last_name',
        'telephone',
        'cpf_cnpj',
        'address',
        'active',
        'deleted'
    ];

    public $timestamps = true;
}
