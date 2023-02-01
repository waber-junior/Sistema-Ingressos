<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Chat;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index (Request $request)
    {

        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $conversations = Chat::conversations();

        $group = '';
        if (sizeof($user->group) > 0) {
            $group = $user->group[0]->name;
        }

        if (strtoupper($group) !== 'ADMINISTRADORES') {
            $conversations->setParticipant($user);
        }

        return response()->json(
            $conversations->setPaginationParams([
                'page' => $request->has('page') ? $request->page : 1,
                'perPage' => $request->has('perPage') ? $request->perPage : 50,
                'sorting' => $request->has('sort') ? $request->sort : 'desc',
                'columns' => [
                    '*'
                ],
                'pageName' => 'test'
            ])
            ->get()
        );
    }

    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $data = $request->all();
        $user = auth()->guard('api')->user();
        $conversation = Chat::createConversation([$user])->makePrivate();
        $conversation->update($data);

        return response()->json($conversation);
    }

    public function addParticipants($chatId, Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $conversation = Chat::conversations()->getById($chatId);
        $add = Chat::conversation($conversation)->addParticipants($request->users);
        return response()->json($add);
    }

    public function sendMessage($chatId, Request $request)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $user = auth()->guard('api')->user();
        $conversation = Chat::conversations()->getById($chatId);
        $message = Chat::message($request->message)
            ->from($user)
            ->to($conversation)
            ->send();
        return response()->json($message);
    }

    public function getMessages($chatId)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        return response()->json(
            Chat::messages()->getById($chatId)
        );
    }
    public function getChat($chatId)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        return response()->json(
            Chat::conversations()->getById($chatId)
        );
    }

    public function update($id, Request $request)
    {
        //
    }

    public function readMessage($chatId)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $user = auth()->guard('api')->user;
        $conversation = Chat::conversations()->getById($chatId);
        $conversation->setParticipant($user)->readAll();

    }

    public function finish ($chatId)
    {
        $user = auth()->guard('api')->user();
        if (!$this->hasPermission($user, 'Chat')) {
            return response()->json(['message' => 'Usuário sem permissão para esta ação!'], 401);
        }
        $settings = ['mute_mentions' => true, 'mute_conversation' => true];
        $conversation = Chat::conversations()->getById($chatId);
        $user = auth()->guard('api')->user;
        return response()->json(
            Chat::conversation($conversation)
                ->getParticipation($user)
                ->update(['settings' => $settings])
        );
    }
}
