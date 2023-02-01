<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Floral;
use App\Models\NFT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function myCampaigns(Request $request)
    {

        $user = auth()->guard('api')->user();
        $perPage = $request->has('perPage') ? $request->perPage: 50;
        $page = $request->has('page') ? $request->page : 1;

        $campaigns = Campaign::with('categories', 'groups', 'users', 'terms');

        $campaigns->whereHas('users', function($q) use($user) {
            $q->where('user_id', $user->id);
        })->orWhereHas('groups',  function($q) use($user) {
            $groups = $user->groups;
            $q->where('group_id', $groups[0]->id);
        })->orWhereHas('categories',  function($q) use($user) {
            $categorie = $user->categories;
            $q->where('categorie_id', $categorie[0]->id);
        });

        if ($request->has('title')) {
            $campaigns->where('title', 'like', '%'. $request->title. '%');
        }


        return response()->json(
            $campaigns->paginate($perPage, '*', null, $page)
        );
    }

    public function myAvatar(Request $request)
    {

        $user = User::findOrFail(auth()->guard('api')->user()->id);

        $validator = Validator::make($request->all(), [
            'avatar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível atualizar foto.'
            ], 400);
        }

        try {

            $user->avatar = $request->avatar;
            $user->save();
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 401);
        }

        return response()->json(['message' => 'Erro ao atualizar imagem'], 400);
    }

    public function myFlorals (Request $request)
    {
        $from = $request->has('startDate') ? $request->startDate : Carbon::now()->subDays(30)->startOfDay();
        $to = $request->has('endDate') ? $request->startDate : Carbon::now()->endOfDay();
        $user = auth()->guard('api')->user();
        $florals = Floral::where('status', 'ACCEPTED')
            ->where('recipient_id', $user->id)
            ->where('accepted_at', '>=', $from)
            ->where('accepted_at', '<=', $to);
        $balance = Floral::BalanceById($user->id);


        $data = [
            'florals' => $florals->get(),
            'balance' => $balance
        ];

        return response()->json(
            $data
        );
    }

    public function myNFTs (Request $request)
    {
        $nfts = NFT::with('user', 'categories', 'classifications');
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page: 1;

        if ($request->has('name')) {
            $nfts->where('name', 'like', '%'. $request->name .'%');
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

        if ($request->has('status')) {
            $nfts->whereHas('user', function($q) use($request) {
                $q->where('status', $request->status);
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

}
