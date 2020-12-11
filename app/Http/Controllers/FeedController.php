<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Image;

class FeedController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function create(Request $request)
    {
        $array = ['error' => ''];

        $data = $request->only(['type', 'body', 'photo']);

        $validator = Validator(
            $data,
            [
                'type' => ['string', 'required', Rule::in(['text', 'photo'])],
                'body' => ['string', 'min:2'],
                'photo' => ['image', 'mimes:jpeg,jpg,png']
            ]
        );

        if ($validator->fails()) {
            $array['error'] = $validator->errors();
            return $this->jsonResponse($array, 400);
        }

        $type = $request->input(['type']);
        $body = $request->input(['body']);
        $photo = $request->file(['photo']);

        switch ($type) {
            case 'text':
                if (!$body) {
                    $array['error'] = 'Type text selecionado, mas foi enviado body';
                    return $this->jsonResponse($array, 400);
                }
            break;
            case 'photo':
                if ($photo) {
                    $fileName = md5(time() . rand(0, 9999)) . '.jpg';
                    $path = public_path('/media/uploads');

                    $image = Image::make($photo->path())->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path . '/' . $fileName);

                    $body = $fileName;
                } else {
                    $array['error'] = 'Type photo selecionado, mas foi enviado photo';
                    return $this->jsonResponse($array, 400);
                }
            break;
        }

        $newPost = new Post();
        $newPost->id_user = $this->loggedUser['id'];
        $newPost->type = $type;
        $newPost->body = $body;
        $newPost->created_at = date('Y-m-d H:i:s');
        $newPost->save();

        return $this->jsonResponse($array);
    }
}
