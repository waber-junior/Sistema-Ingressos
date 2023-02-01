<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index (Request $request)
    {
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page : 1;

        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Post')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }

        $posts = Post::with('comments', 'categories', 'groups', 'users');

        return response()->json($posts->paginate($perPage, '*', null, $page));
    }

    public function store (Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Adicionar Post')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'description' => 'required|min:3',
            'groups' => 'required',
            'users' => 'required',
            'categories' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível gravar post.'
            ], 400);
        }
        $groups = $data['groups'];
        $users = $data['users'];
        $categories = $data['categories'];
        unset($data['groups']);
        unset($data['users']);
        unset($data['categories']);

        try {
            $post = Post::create($data);

            $this->syncCategories($post, $categories);
            $this->syncGrous($post, $groups);
            $this->syncUsers($post, $users);

            return response()->json($post);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function update ($postId, Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Adicionar Post')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'description' => 'required|min:3',
            'groups' => 'required',
            'users' => 'required',
            'categories' => 'required',
        ]);

        $groups = $data['groups'];
        $users = $data['users'];
        $categories = $data['categories'];

        unset($data['groups']);
        unset($data['users']);
        unset($data['categories']);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível atualizar post.'
            ], 400);
        }

        try {
            $post = Post::findOrFail($postId);
            $post->update($data);

            $this->syncCategories($post, $categories);
            $this->syncGroups($post, $groups);
            $this->syncUsers($post, $users);

            return response()->json($post);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    public function comment ($postId, Request $request)
    {
        $_user = auth()->guard('api')->user();
        if (!$this->hasPermission($_user, 'Comentar Post')) {
            return response()->json(['message' => 'Usuário sem premissão para esta ação!'], 401);
        }
        $data = $request->all();
        $user = auth()->guard('api')->user();
        $validator = Validator::make($data, [
            'description' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível comentar.'
            ], 400);
        }

        try {
            $post = Post::findOrFail($postId);
            $comment = Comment::create(array_merge($data, ['post_id' => $post->id, 'user_id' => $user->id]));
            return response()->json($comment);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy ($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $post->delete();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    protected function syncCategorie(Post $post, $data)
    {

        try {
            $categories = $post->categories()->sync($data);
            return $categories;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    protected function syncGroups(Post $post, $data)
    {

        try {
            $groups = $post->groups()->sync($data);
            return $groups;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    protected function syncUsers(Post $post, $data)
    {

        try {
            $users = $post->users()->sync($data);
            return $users;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
