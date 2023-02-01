<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index (Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Permissões')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $permissions = Permission::query();
        $perPage = $request->get('perPage') ? $request->perPage : 50;
        $page = $request->get('page') ? $request->page : 1;

        if ($request->has('name')) {
            $permissions->where('name', 'like', '%'. $request->name .'%');
        }

        return response()->json($permissions->paginate($perPage, '*', null, $page));
    }

    public function show ($id)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Permissões')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $permissions = Permission::findOrFail($id);
        $permissions->rules;

        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Permissões')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
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
            $permission = Permission::create($data);
            return response()->json($permission);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update($permissionId, Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Permissões')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
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
            $permission = Permission::findOrFail($permissionId);
            $permission->update($data);
            return response()->json($permission);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy ($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);

        try {
            $permission->rules()->detach();
            $permission->delete();
            return response()->json(['message' => 'Deletado com sucesso !!']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

}
