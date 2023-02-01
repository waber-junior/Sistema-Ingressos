<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UUID;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Floral extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $table = 'florals';

    protected $fillable = [
        'recipient_id',
        'status',
        'type',
        'sender_id',
        'amount',
        'observation',
        'accepted_at'
    ];

    protected $dates = ['deleted_at'];

    public function sender ()
    {
        return $this->belongsTo(User::class, 'id', 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'id', 'recipient_id');
    }

    static public function BalanceById($userId)
    {
        $input = DB::table('florals')
            ->where('recipient_id', $userId)
            ->where('status', 'ACCEPTED')
            ->where('status', 'INPUT')
            ->sum('type');

        $output = DB::table('florals')
            ->where('recipient_id', $userId)
            ->where('status', 'ACCEPTED')
            ->where('status', 'OUTPUT')
            ->sum('type');

        return ($input - $output);
    }

}
