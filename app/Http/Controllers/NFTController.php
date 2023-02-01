<?php

namespace App\Http\Controllers;

use App\Models\NFT;
use App\Models\NFTCategorie;
use App\Models\NFTClassification;
use App\Models\NFTUser;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NFTController extends Controller
{
    public function index (Request $request)
    {

        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'NFT')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $nfts = NFT::with('user', 'categories', 'classifications');
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page: 1;

        if ($request->has('name')) {
            $nfts->where('name', 'like', '%'. $request->name .'%');
        }

        if ($request->has('users')) {
            $nfts->whereHas('user', function($q) use($request) {
                $q->whereIn('recipient_id', $request->users);
            });
        }

        if ($request->has('status')) {
            $nfts->where('status', $request->status);
        }

        if ($request->hasHeader('admin')) {
            if (!$this->hasPermission($_user, 'Campanhas')) {
                return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
            }
        }

        if ($request->has('categories')) {
            $nfts->whereHas('categories', function($q) use($request) {
                $q->whereIn('nft_categorie_id', $request->categories);
            });
        }
        if ($request->has('classifications')) {
            $nfts->whereHas('classifications', function($q) use($request) {
                $q->whereIn('nft_classification_id', $request->classifications);
            });
        }

        return response()->json(
            $nfts->paginate($perPage,
            '*',
            null,
            $page
            )
        );
    }

    public function store (Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Cadastrar NFT')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'status' => 'in:ACTIVE,SUSPEND,INACTIVE',
            'description' => 'required',
            'image' => 'required',
            'categories' => 'required',
            'classifications' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Erro ao criar termos!!'
            ], 400);
        }

        $categories = $data['categories'];
        $classification = $data['classifications'];
        unset($data['categories']);
        unset($data['classifications']);

        try {
            $NFT = NFT::create($request->all());
            return response()->json($NFT);

            $NFT->categories()->sync($categories);
            $NFT->classifications()->sync($classification);
        } catch (Exception $e) {
            return response()->json([
                'messge' => 'Erro ao criar NFT',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function update ($id, Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Cadastrar NFT')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'status' => 'in:ACTIVE,SUSPEND,INACTIVE',
            'description' => 'required',
            'classification' => 'required',
            'categories' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Erro ao criar termos!!'
            ], 400);
        }

        $categories = $data['categories'];
        $classification = $data['classification'];
        unset($data['categories']);
        unset($data['classification']);

        try {
            $NFT = NFT::findOrFail($id);
            $NFT->update($request->all());

            $NFT->categories()->sync($categories);
            $NFT->classifications()->sync($classification);

            return response()->json($NFT);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar NFT',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy ($id)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Cadastrar NFT')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $nft = NFT::findOrFail($id);
        try {

            $nft->update(['status' => 'INACTIVE']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao INATIVAR NFT',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getCategorie ()
    {
        $categories = NFTCategorie::all();
        return response()->json($categories);
    }

    public function addCategorie(Request $request)
    {
        try {
            $_user = auth()->guard('api')->user();
            if (!$this->hasPermission($_user, 'Classificação e Categoria')) {
                return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
            }
            $categorie = NFTCategorie::create($request->all());
            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function updateCategorie($id, Request $request)
    {
        try {
            $_user = auth()->guard('api')->user();
            if (!$this->hasPermission($_user, 'Classificação e Categoria')) {
                return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
            }
            $categorie = NFTCategorie::findOrFail($id);
            $categorie->update($request->all());
            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao atualizar categoria'], 400);
        }
    }

    public function removeCategorie($id)
    {
        $_user = auth()->guard('api')->user();
            if (!$this->hasPermission($_user, 'Classificação e Categoria')) {
                return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
            }
        try {
            $categorie = NFTCategorie::findOrFail($id);
            $categorie->delete();
            return response()->json(['message' => 'Deletado com sucesso !!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function getClassifications()
    {
        return response()->json(
            NFTClassification::all()
        );
    }

    public function addClassification(Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Classificação e Categoria')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        try {

            $classification = NFTClassification::create($request->all());
            return response()->json($classification);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }
    public function updateClassification($id, Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Classificação e Categoria')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        try {
            $classification = NFTClassification::findOrFail($id);
            $classification->update($request->all());
            return response()->json($classification);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function removeClassification($id)
    {
        $_user = auth()->guard('api')->user();
            if (!$this->hasPermission($_user, 'Classificação e Categoria')) {
                return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
            }
        try {
            $classification = NFTClassification::findOrFail($id);
            $classification->delete();
            return response()->json(['message' => 'Deletado com sucesso !!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function transferNft ($id, Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Transferência NFT')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $data = $request->all();

        try {
            $nft = NFT::findOrFail($id);
            $userNft = $nft->user;
            if (!$userNft) {
                $transfer = NFTUser::create(array_merge($data, ['nft_id' => $nft->id, 'sender_id' => $user->id]));

            } else {
                $userNft->sender_id = $user->id;
                $userNft->recipient_id = $request->recipient_id;
                $userNft->status = 'PENDING';
                $userNft->accepted_at = null;
                $userNft->save();
                $reciver = User::findOrFail($data['recipient_id']);
                DB::table('nft_transactions')->insert([
                    'created_by' => $nft->sender_id,
                    'nft_id' => $nft->id,
                    'details' => 'O usuário '.$user->name.' enviou a NFT para '.$reciver->name,
                    'action' => 'Transferência de NFT'
                ]);
            }

            return response()->json($transfer);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao transferir NFT'], 400);
        }
    }

    public function status ($id, Request $request)
    {

        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Transferência NFT')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        try {
            $nft = NFT::findOrFail($id);
            $userNft = $nft->user;
            $userNft->status = $request->status;
            if ($request->status === 'ACCEPTED') {
                $userNft->accepted_at = Carbon::now();
            }
            $userNft->save();

            $action = '';
            $details = '';
            $reciver = User::findOrFail($nft->recipient_id);
            if ($request->status === 'ACCEPTED') {
                $action ='Aceite da trasnferência de NFT';
                $details = 'O usuário '.$reciver->name.' aceitou a movimentação';
            } else if ($request->status === 'REJECT') {
                $action ='Rejeição da trasnferência de NFT';
                $details = 'O usuário '.$reciver->name.' rejeitou a movimentação';
            } else {
                $action = 'Mudança de status da NFT';
                $details ='O usuário '.$user->name.' mudou o status para '.$request->status;
            }
            DB::table('nft_transactions')->insert([
                'created_by' => $nft->sender_id,
                'nft_id' => $nft->id,
                'details' => $details,
                'action' => $action
            ]);
            return response()->json($nft);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao atualizar status da NFT'], 400);
        }
    }

    public function audit(Request $request)
    {
        $audit = DB::table('nft_transactions');
        $from = $request->has('startDate') ? $request->startDate : Carbon::now()->subDays(30)->startOfDay();
        $to = $request->has('endDate') ? $request->endDate : Carbon::now()->endOfDay();

        $audit->where('created_at', '>=', $from);
        $audit->where('created_at', '<=', $to);

        return response()->json($audit);
    }
}
