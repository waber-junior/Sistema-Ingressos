<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresses extends Model
{
    use HasFactory;

    protected $table = 'adresses';

    protected $fillable = [
        'street',
        'number',
        'complement',
        'neighborhood',
        'zipcode',
        'city',
        'state'
    ];
}
