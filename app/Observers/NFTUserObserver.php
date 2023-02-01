<?php

namespace App\Observers;

use App\Models\NFTUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NFTUserObserver
{
    /**
     * Handle the NFTUser "created" event.
     *
     * @param  \App\Models\NFTUser  $nFTUser
     * @return void
     */
    public function created(NFTUser $nFTUser)
    {
        $user = User::findOrFail($nFTUser->sender_id);
        DB::table('nft_transactions')->insert([
            'created_by' => $nFTUser->sender_id,
            'nft_id' => $nFTUser->id,
            'action' => 'Transferência de NFT',
            'details' => 'O usuário '.$user->name.' transferiu a NFT pela primeira vez.',
        ]);

    }

    /**
     * Handle the NFTUser "updated" event.
     *
     * @param  \App\Models\NFTUser  $nFTUser
     * @return void
     */
    public function updated(NFTUser $nFTUser)
    {
        //
    }

    /**
     * Handle the NFTUser "deleted" event.
     *
     * @param  \App\Models\NFTUser  $nFTUser
     * @return void
     */
    public function deleted(NFTUser $nFTUser)
    {
        DB::table('nft_transactions')->insert([
            'created_by' => $nFTUser->sender_id,
            'nft_id' => $nFTUser->id,
            'action' => 'Transferência de NFT',
            'details' => 'Primeira transferência de NFT para o usuário '.$nFTUser->recipient_id
        ]);
    }

    /**
     * Handle the NFTUser "restored" event.
     *
     * @param  \App\Models\NFTUser  $nFTUser
     * @return void
     */
    public function restored(NFTUser $nFTUser)
    {
        //
    }

    /**
     * Handle the NFTUser "force deleted" event.
     *
     * @param  \App\Models\NFTUser  $nFTUser
     * @return void
     */
    public function forceDeleted(NFTUser $nFTUser)
    {
        //
    }
}
