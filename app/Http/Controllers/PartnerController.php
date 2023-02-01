<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function index (Request $request)
    {
        $partners = Partner::query();
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page : 1;

        if ($request->has('name')) {
            $partners->where('name', 'like', '%'. $request->name .'%');
        }

        if ($request->has('deadline')) {
            $partners->where('deadline', Carbon::parse($request->deadline)->format('Y-m-d'));
        }

        return response()->json(
            $partners->paginate($perPage, '*', null, $page)
        );
    }

    public function store (Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Cadastro Patrocinador')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar patrocinador.'
            ], 400);
        }

        try {
            $partner = Partner::create($data);
            return response()->json($partner);
        } catch (Exception $e) {
            return response()->json($e, 400);
        }
    }

    public function update ($id, Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Cadastro Patrocinador')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar patrocinador.'
            ], 400);
        }

        try {
            $partner = Partner::findOrFail($id);
            $partner->update($data);
            return response()->json($partner);
        } catch (Exception $e) {
            return response()->json($e, 400);
        }
    }

    public function destroy($id)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Cadastro Patrocinador')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        try {
            $partner = Partner::findOrFail($id);
            $partner->delete();
            return response()->json(['message' => 'Deletado com sucesso']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
