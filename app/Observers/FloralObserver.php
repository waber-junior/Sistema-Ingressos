<?php

namespace App\Observers;

use App\Models\Floral;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FloralObserver
{
    /**
     * Handle the Floral "created" event.
     *
     * @param  \App\Models\Floral  $floral
     * @return void
     */
    public function created(Floral $floral)
    {
        if ($floral->type === 'INPUT') {

            DB::table('florals_transactions')->insert([
                'created_by' => $floral->sender_id,
                'floral_id' => $floral->id,
                'action' => 'Transferência de Floral',
                'details' => 'Recebendo Floral no valor de '.$floral->amount
            ]);
        } else {
            DB::table('florals_transactions')->insert([
                'created_by' => $floral->sender_id,
                'floral_id' => $floral->id,
                'action' => 'Transferência de Floral',
                'details' => 'Enviando Floral no valor de '.$floral->amount
            ]);
        }
    }

    /**
     * Handle the Floral "updated" event.
     *
     * @param  \App\Models\Floral  $floral
     * @return void
     */
    public function updated(Floral $floral)
    {
        $action = '';
        if ($floral->status === 'ACCEPTED') {
            $action = 'Aceite da transferencia de Floral';
        } else if ($floral->status === 'REJECTED') {
            $action = 'Rejeição da transferência de floral';
        } else {
            $action = 'Mudança de status Floral';
        }

        DB::table('florals_transactions')->insert([
            'created_by' => $floral->sender_id,
            'floral_id' => $floral->id,
            'details' => 'O status da floral foi alterado para '.$floral->status,
            'action' => $action
        ]);
    }

    /**
     * Handle the Floral "deleted" event.
     *
     * @param  \App\Models\Floral  $floral
     * @return void
     */
    public function deleted(Floral $floral)
    {
        $user = User::findOrFail($floral->sender_id);
        DB::table('florals_transactions')->insert([
            'created_by' => $floral->sender_id,
            'floral_id' => $floral->id,
            'details' => 'A Floral foi deletada pelo usuário '.$user->name,
            'action' => 'Movimentação de Floral Cancelada'
        ]);
    }

    /**
     * Handle the Floral "restored" event.
     *
     * @param  \App\Models\Floral  $floral
     * @return void
     */
    public function restored(Floral $floral)
    {
        //
    }

    /**
     * Handle the Floral "force deleted" event.
     *
     * @param  \App\Models\Floral  $floral
     * @return void
     */
    public function forceDeleted(Floral $floral)
    {
        //
    }
}
