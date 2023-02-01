<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TermController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->has('perPage') ? $request->perPage : 30;
        $page = $request->has('page') ? $request->page : 1;
        $terms = Term::query();

        if ($request->has('name')) {
            $terms->where('name', 'like', '%'. $request->name .'%');
        }

        if ($request->has('type')) {
            $terms->where('type', $request->type);
        }

        return response()->json($terms->paginate($perPage, ['*'], null, $page));
    }

    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Termos')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Erro ao criar termos!!'
            ], 400);
        }

        try {
            $term = Term::create($request->all());
            return response()->json($term);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro desconhecido ao criar', 'errors' => $e->getMessage()], 400);
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Erro ao criar termos!!'
            ], 400);
        }

        try {

            $term = Term::findOrFail($id);
            $term->update($request->all());
            return response()->json($term);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro desconhecido ao atualizar', 'errors' => $e->getMessage()], 400);
        }
    }

    public function destroy ($id)
    {

        try {

            $term = Term::findOrFail($id);
            $term->update(['active' => false]);
            return response()->json($term);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro desconhecido ao atualizar', 'errors' => $e->getMessage()], 400);
        }
    }

}
