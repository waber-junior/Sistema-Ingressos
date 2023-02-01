<?php

namespace App\Http\Controllers;

use App\Models\Floral;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FloralController extends Controller
{
    public function index (Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Floral')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }

        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page : 1;

        $floral = Floral::with('recipient', 'sender');

        $group = '';
        if (sizeof($user->group) > 0) {
            $group = $user->group[0]->name;
        } else {
            return response()->json(['message'=> 'usuário sem acesso'], 401);
        }

        if (strtoupper($group) !== 'ADMINISTRADORES') {
            $floral->where('recipient_id', $user->id);
        }

        if ($request->has('type')) {
            $floral->where('type', $request->type);
        }

        if ($request->has('startDate')) {
            $floral->where('created_at', '>=', $request->startDate);
        }

        if ($request->has('endDate')) {
            $floral->where('created_at', '<=', $request->endDate);
        }
        if ($request->has('accepted_at')) {
            $floral->where('accepted_at', $request->accepted_at);
        }

        return response()->json(
            $floral->paginate($perPage, '*', null, $page)
        );

    }

    public function store (Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Movimentação de Floral')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $data = $request->all();

         $validator = Validator::make($data, [
            'status' => 'in:ACCEPTED,REJECTED,PENDING',
            'type' => 'in:INPUT,OUTPUT',
            // 'recipient_id' => 'required',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }
        $group = '';
        if (sizeof($user->group) > 0) {
            $group = $user->group[0]->name;
        } else {
            return response()->json(['message'=> 'usuário sem acesso'], 401);
        }
        // VERIFICO SE POSSUI SALDO OU SE É ADMINISTRADOR
        if (strtoupper($group) !== 'ADMINISTRADORES') {
            //Log::info("GRUPO --> ".$group);

            $balance = Floral::BalanceById($user->id);
            if ($balance <= 0) {
                return response()->json(['message' => 'Usuário sem saldo para esta transação'], 400);
            }
        }
        $data = array_merge($data,['sender_id' => $user->id]);
        // Log::info($data);
        try {
            $floral = Floral::create($data);
            return response()->json($floral);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 400]);
        }
    }

    public function update($id, Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Movimentação de Floral')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $floral = Floral::findOrFail($id);
        if (!$request->has('status')) {
            return response()->json(['message' => 'STATUS não informado'], 400);
        }
        $floral->status = $request->status;
        if ($request->status === 'ACCEPTED') {
            $floral->accepted_at = Carbon::now();
        }

        try {
            $floral->save();

            return response()->json($floral);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 400]);
        }
    }

    public function destroy($id)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Movimentação de Floral')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $floral = Floral::findOrFail($id);
        if ($floral->status !== 'PENDING') {
            return response()->json(['message' => 'Esta transação não pode mais ser deletada.'], 400);
        }

        try {
            $floral->delete();
            return response()->json(['message' => 'Floral deletada com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function audit(Request $request)
    {
        $audit = DB::table('florals_transactions');
        $from = $request->has('startDate') ? $request->startDate : Carbon::now()->subDays(30)->startOfDay();
        $to = $request->has('endDate') ? $request->endDate : Carbon::now()->endOfDay();

        $audit->where('created_at', '>=', $from);
        $audit->where('created_at', '<=', $to);

        return response()->json($audit);
    }
}
