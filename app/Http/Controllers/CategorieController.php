<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{

    public function index (Request $request)
    {
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page : 1;

        $categories = Categorie::query();

        if ($request->has('name')) {
            $categories->where('name', 'like', '%'. $request->name. '%');
        }

        if ($request->has('active')) {
            $categories->where('active', $request->active);
        }

        return response()->json(
            $categories->paginate($perPage, '*', null, $page)
        );
    }

    public function store (Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível gravar categoria.'
            ], 400);
        }
        try {
            $categorie = Categorie::create($data);
            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao gravar categoria.',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function update ($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível gravar categoria.'
            ], 400);
        }
        try {

            $categorie = Categorie::findOrFail($id);
            $categorie->update($data);

            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar categoria.',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy ($id)
    {

        try {

            $categorie = Categorie::findOrFail($id);
            $categorie->active = false;
            $categorie->save();

            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar categoria.',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

}
