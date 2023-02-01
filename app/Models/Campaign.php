<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
        'image',
        'created_by'
    ];

    public function terms()
    {
        return $this->belongsToMany(Term::class, 'campaigns_has_terms', 'campaign_id', 'term_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Categorie::class, 'campaigns_has_categories', 'campaign_id', 'categorie_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'campaigns_has_groups', 'campaign_id', 'group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'campaigns_has_users', 'campaign_id', 'user_id');
    }
}
