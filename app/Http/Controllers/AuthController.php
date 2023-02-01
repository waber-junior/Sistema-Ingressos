<?php

namespace App\Http\Controllers;

use App\Models\Adresses;
use App\Models\Person;
use App\Models\User;
use App\Models\UserDocument;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }

        if (Auth::attempt($data)) {
            $user = Auth::user();
            if ($user->status === 0) {
                return response()->json(['message' => 'Usuário ainda não foi ativado/ aprovado'], 401);
            }
            $user->person;
            $group = $user->group;
            if (sizeof($group) === 0) {
                return response()->json(['message' => 'Usuário sem grupo de acesso!!'], 401);
            }

            if (!$user->person->approved_at) {
                return response()->json(['message' => 'Usuário aguardando aprovação!!'], 401);
            }

            $user->permissions = $group->rules;

            return response([
                'token' => $user->createToken('madeFy')->accessToken,
                'user' => $user->person
            ]);
        }

        return response(['message' => 'usuaário e/ou senha inválidos'], 401);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'cpf' => 'required|unique:persons',
            'email' => 'required|unique:users',
            'attachments' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }

        $data = $request->all();

        $dataAddress = isset($data['address']) ? $data['address'] : [];
        $dataUser = array(
            'password' => bcrypt($data['password']),
            'name' => $data['name'],
            'email' => $data['email'],
            'nickname' => uniqid()
        );
        $dataPerson = array(
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'rg' => isset($data['rg']) ? $data['rg']: '',
            'phone' => isset($data['phone']) ? $data['phone'] : '',
            'is_whatsapp' => isset($data['is_whatsapp']) ? $data['is_whatsapp'] : false
        );

        try {
            if (sizeof($dataAddress) > 0) {
                if ( !isset($dataAddress['street']) ) {
                    return response()->json(['message' => 'Desculpe, ao informar o endereço é necessário que informe o nome da rua.'], 401);
                }
                $address = Adresses::create($dataAddress);
                $dataPerson['addres_id'] = $address->id;
            }

            if ($request->has('avatar')) {
                $dataUser['avatar'] = $request->avatar;
            }

            $user = User::create($dataUser);

            foreach ($data['attachments'] as $attachment) {
                UserDocument::create([
                    'path' => $attachment['attachment'],
                    'user_id' => $user->id
                ]);
            }

            $dataPerson['user_id'] = $user->id;

            $person = Person::create($dataPerson);

            if (Auth::attempt(['email' => $user->email, 'password' => $data['password']])) {
                $user->createToken('madeFy')->accessToken;
            }
            $token = $user->createToken('madeFy')->accessToken;

            return response([ 'user' => $person, 'token' => $token]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Desculpe, houve um erro ao registar usuário.',
                'error' => $e->getMessage()
            ], 401);
        }


    }

    public function logout (Request $request) {

        $token = Auth::user()->accessToken;
        $token->revoke();
        return response()->json(['message' => 'Usuário deslogado']);
    }

    public function changePassword (Request $request)
    {
        if (Auth::guest()) {
            return response()->json(['message' => 'Usuário não autenticado'], 400);
        }
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível atualizar sua senha.'
            ], 400);
        }

        $userUpdated = User::findOrFail($user->id);
        $userUpdated->password = bcrypt($request->new_password);
        try {
            $userUpdated->save();
            return response()->json(['message' => 'Senha Alterada com sucesso']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 400]);
        }

    }

    public function forgotPassword (Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        try {
            $reset = ($status === Password::RESET_LINK_SENT);
            return response()->json(['status' => $status, 'reset' => $reset ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function resetPasswordByToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível resetar senha'
            ], 400);
        }
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {

                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );
            $reset = ($status === Password::PASSWORD_RESET);

            return response()->json(['status' => $status, 'reset' => $reset]);
        } catch (Exception $e) {
            return response()->json($e->getMessage, 400);
        }
    }

    public function me ()
    {
        $user = auth()->guard('api')->user();
        $person = $user->person;
        $person->address;
        $person->user = $user;

        return response()->json($person);
    }
}
