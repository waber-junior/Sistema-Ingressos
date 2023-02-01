<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentsRequest;
use App\Http\Requests\ImageStoreRequest;
use App\Models\Adresses;
use App\Models\Person;
use App\Models\User;
use App\Models\UserDocument;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Usuário')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'cpf' => 'required|numeric|min:11',
            'email' => 'required|unique:users',
            'group' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }

        if (!$this->cpfIsValid($request->cpf)) {
            return response()->json(['message' => 'Este CPF não é válido'], 400);
        }

        $data = $request->all();

        $dataAddress = isset($data['address']) ? $data['address'] : [];
        $dataUser = array(
            'password' => bcrypt($data['password']),
            'name' => $data['name'],
            'email' => $data['email']
        );
        $dataPerson = array(
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'rg' => isset($data['rg']) ? $data['rg']: '',
            'phone' => isset($data['phone']) ? $data['phone'] : '',
            'is_whatsapp' => isset($data['is_whatsapp']) ? $data['is_whatsapp'] : false,
            'approved_at' => Carbon::now(),
            'approved_by' => $user->id
        );

        try {
            if (sizeof($dataAddress) > 0) {
                if ( !isset($dataAddress['street']) ) {
                    return response()->json(['message' => 'Desculpe, ao informar o endereço é necessário que informe o nome da rua.'], 400);
                }
                $address = Adresses::create($dataAddress);
                $dataPerson['addres_id'] = $address->id;
            }

            $user = User::create($dataUser);
            $user->group()->sync([$data['group']]);
            $user->group;

            $dataPerson['user_id'] = $user->id;

            $person = Person::create($dataPerson);

            $person->user = $user;
            if ($address) {
                $person->address = $address;
            }

            return response([ 'user' => $person ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Desculpe, houve um erro ao registar usuário.',
                'error' => $e->getMessage()
            ], 401);
        }

    }


    public function index (Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Usuário')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }

        $users = Person::with('user');
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page : 1;

        if ($request->has('name')) {
            $users->where('name', 'like', '%'. $request->name. '%');
        }

        if ($request->has('cpf')) {
            $users->where('cpf', 'like', '%'. $request->cpf. '%');
        }

        if ($request->has('email')) {
            $users->whereHas('user', function ($q) use($request) {
                $q->where('email', 'like', '%'. $request->email. '%');
            });
        }

        if ($request->has('id')) {
            return response()->json(
                $users->where('id', $request->id)
                ->with('user', 'address', 'attachments')
                ->first()
            );
        }

        if ($request->has('deleted_at')) {
            $users->withTrashed();
        }

        return response()->json(
            $users->paginate($perPage, '*', null, $page)
        );
    }

    public function update ($personId, Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Usuário')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|min:3',
            'cpf' => 'required|numeric|min:11',
            'email' => 'required',
            'group' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível atualizar o usuário.'
            ], 400);
        }

        if (!$this->cpfIsValid($data['cpf'])) {
            return response()->json(['message' => 'Este CPF não é válido'], 400);
        }

        $person = Person::findOrFail($personId);
        $address = $person->address;
        $user = $person->user;

        $dataAddress = isset($data['address']) ? $data['address'] : [];
        $dataUser = array(
            'name' => $data['name'],
            'email' => $data['email']
        );

        if (isset($data['password'])) {
            $dataUser['password'] = bcrypt($data['password']);
        }

        $dataPerson = array(
            'name' => $data['name'],
            'cpf' => preg_replace( '/[^0-9]/is', '', $data['cpf'] ),
            'rg' => isset($data['rg']) ? $data['rg']: '',
            'phone' => isset($data['phone']) ? (preg_replace( '/[^0-9]/is', '', $data['phone'])) : '',
            'is_whatsapp' => isset($data['is_whatsapp']) ? $data['is_whatsapp'] : false
        );

        try {
            if (sizeof($dataAddress) > 0) {
                if ( !isset($dataAddress['street']) ) {
                    return response()->json(['message' => 'Desculpe, ao informar o endereço é necessário que informe o nome da rua.'], 401);
                }
                if ($address) {
                    $address->update($dataAddress);
                } else {
                    $address = Adresses::create($dataAddress);
                }

                $dataPerson['addres_id'] = $address->id;
            }

            $user->update($dataUser);
            $dataPerson['user_id'] = $user->id;

            $person->update($dataPerson);
            $user->group()->sync([$data['group']]);
            return response([ 'user' => $person ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Desculpe, houve um erro ao registar usuário.',
                'error' => $e->getMessage()
            ], 401);
        }

    }

    public function destroy ($personId)
    {

        $person = Person::findOrFail($personId);
        $user = $person->user;
        try {
            $person->delete();
            $user->active = false;
            $user->save();
            return response()->json(['message' => 'Usuário deletado com sucesso']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function avatar($userId, Request $request)
    {

        $user = User::findOrFail($userId);

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
            //$path = $request->file('avatar')->store('avatars', 'azure');

            $user->avatar = $request->avatar;
            $user->save();
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 401);
        }

        return response()->json(['message' => 'Erro ao atualizar imagem'], 400);
    }

    public function attachments($userId, Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Usuário')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $user = User::findOrFail($userId);

        $validator = Validator::make($request->all(), [
            'attachment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível atualizar foto.'
            ], 400);
        }

        try {

            $document = UserDocument::create([
                'path' => $request->attachment,
                'user_id' => $user->id
            ]);

            return response()->json($document);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return response()->json(['message' => 'Erro ao atualizar imagem'], 400);

    }
}

