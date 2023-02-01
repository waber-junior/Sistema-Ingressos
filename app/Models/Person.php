<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'persons';

    protected $fillable = [
        'name',
        'user_id',
        'cpf',
        'rg',
        'birthdate',
        'phone',
        'is_whatsapp',
        'address_id',
        'approved_at',
        'approved_by'
    ];

    protected $dates = ['deleted_at'];

    public function address ()
    {
        return $this->hasOne(Adresses::class, 'id', 'address_id');
    }

    public function user ()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function attachments ()
    {
        return $this->hasMany(UserDocument::class, 'user_id', 'user_id');
    }
}
