<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UUID;
use Illuminate\Database\Eloquent\SoftDeletes;

class NFTUser extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $table = 'nft_users';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sender_id',
        'nft_id',
        'recipient_id',
        'status'
    ];
}
