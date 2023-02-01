<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function rules ()
    {
        return $this->belongsToMany(Permission::class, 'permissions_has_group', 'group_id', 'permission_id');
    }
}
