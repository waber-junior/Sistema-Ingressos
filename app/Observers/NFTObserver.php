<?php

namespace App\Observers;

use App\Models\NFT;

class NFTObserver
{
    /**
     * Handle the NFT "created" event.
     *
     * @param  \App\Models\NFT  $nFT
     * @return void
     */
    public function created(NFT $nFT)
    {
        //
    }

    /**
     * Handle the NFT "updated" event.
     *
     * @param  \App\Models\NFT  $nFT
     * @return void
     */
    public function updated(NFT $nFT)
    {
        //
    }

    /**
     * Handle the NFT "deleted" event.
     *
     * @param  \App\Models\NFT  $nFT
     * @return void
     */
    public function deleted(NFT $nFT)
    {
        //
    }

    /**
     * Handle the NFT "restored" event.
     *
     * @param  \App\Models\NFT  $nFT
     * @return void
     */
    public function restored(NFT $nFT)
    {
        //
    }

    /**
     * Handle the NFT "force deleted" event.
     *
     * @param  \App\Models\NFT  $nFT
     * @return void
     */
    public function forceDeleted(NFT $nFT)
    {
        //
    }
}
