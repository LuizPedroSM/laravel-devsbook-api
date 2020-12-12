<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;

class PostController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function like($id)
    {
        $array = ['error' => ''];

        // 1. verificar se o post existe
        $postExists = Post::find($id);
        if($postExists){
            // 2. verificar se o usuário logado deu like no post
            $isLiked = PostLike::where('id_post', $id)
                ->where('id_user', $this->loggedUser['id'])->count();

            if($isLiked > 0){
                // 2.1 se sim, remover
                $pl = PostLike::where('id_post', $id)
                    ->where('id_user', $this->loggedUser['id'])
                    ->first();
                $pl->delete();

               $array['isLiked'] = false;
            } else {
                // 2.2 se não, adicionar
                $newPostLike = new PostLike();
                $newPostLike->id_post = $id;
                $newPostLike->id_user = $this->loggedUser['id'];
                $newPostLike->created_at = date('Y-m-d H:i:s');
                $newPostLike->save();

               $array['isLiked'] = true;
            }

            $likeCount = PostLike::where('id_post', $id)->count();
            $array['likeCount'] = $likeCount;

        } else {
            $array['error'] = 'Post não existe.';
            return $this->jsonResponse($array);
        }


        return $this->jsonResponse($array);
    }

    public function comment(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator($request->only(['txt']), ['txt' => ['required', 'min:2']]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors();
            return $this->jsonResponse($array, 400);
        }

        $txt = $request->input('txt');

        $postExists = Post::find($id);

        if ($postExists) {
            $newComment = new PostComment();
            $newComment->id_post = $id;
            $newComment->id_user = $this->loggedUser['id'];
            $newComment->created_at = date('Y-m-d H:i:s');
            $newComment->body = $txt;
            $newComment->save();

        } else {
            $array['error'] = 'Post não existe';
        }

        return $this->jsonResponse($array);
    }
}
