<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'module'
    ];

    public function rules ()
    {
        return $this->belongsToMany(Group::class, 'permissions_has_group', 'permission_id', 'group_id');
    }
}
