<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFTCategorie extends Model
{
    use HasFactory;

    protected $table = 'nft_categories';

    protected $fillable = [
        'name'
    ];
}
