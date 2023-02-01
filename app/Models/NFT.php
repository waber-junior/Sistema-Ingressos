<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NFT extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nfts';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'image',
        'status'
    ];

    public function categories()
    {
        return $this->belongsToMany(NFTCategorie::class, 'nfts_has_categories', 'nft_id', 'nft_categorie_id');
    }

    public function classifications()
    {
        return $this->belongsToMany(NFTClassification::class, 'nfts_has_classifications', 'nft_id', 'nft_classification_id');
    }

    public function user ()
    {
        return $this->belongsTo(NFTUser::class, 'nft_id');
    }
}

