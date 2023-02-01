<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'description',
        'attachment',
    ];

    public function categories()
    {
        return $this->belongsToMany(Categorie::class, 'posts_has_categories', 'post_id', 'categorie_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'posts_has_groups', 'post_id', 'group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'posts_has_users', 'post_id', 'user_id');
    }

    public function comments ()
    {
        return $this->belongsTo(Comment::class, 'post_id');
    }
}
