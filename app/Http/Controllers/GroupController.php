<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function index (Request $request)
    {
        $groups = Group::query();
        $perPage = $request->get('perPage') ? $request->perPage : 50;
        $page = $request->get('page') ? $request->page : 1;

        if ($request->has('name')) {
            $groups->where('name', 'like', '%'. $request->name .'%');
        }

        return response()->json($groups->paginate($perPage, '*', null, $page));
    }

    public function show ($id)
    {
        $group = Group::findOrFail($id);
        $group->rules;
        return response()->json($group);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar permissão.'
            ], 400);
        }

        try {
            $group = Group::create($data);
            return response()->json($group);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update($permissionId, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar permissão.'
            ], 400);
        }

        try {
            $group = Group::findOrFail($permissionId);
            $group->update($group);
            return response()->json($group);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy ($permissionId)
    {
        $permission = Group::findOrFail($permissionId);

        try {
            $permission->rules()->detach();
            $permission->delete();
            return response()->json(['message' => 'Deletado com sucesso !!']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function syncRules ($groupId, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'rules' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível sincronizar permissões do grupo.'
            ], 400);
        }
        $group = Group::findOrFail($groupId);

        try {
            $rules = $group->rules()->sync($data['rules']);
            return response()->json($rules);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }
}
