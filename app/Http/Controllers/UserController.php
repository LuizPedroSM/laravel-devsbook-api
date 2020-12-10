<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Image;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function update(Request $request)
    {
        $array = ['error' => ''];
        $data = $request->only(['name', 'email', 'birthdate', 'city', 'work', 'password', 'password_confirm']);

        $validator = Validator(
            $data,
            [
                'name' => ['string', 'min:2', 'max:100'],
                'email' => ['string', 'email'],
                'birthdate' => ['date'],
                'city' => ['string'],
                'work' => ['string'],
                'password' => ['string', 'min:6'],
                'password_confirm' => ['string', 'min:6'],
            ]
        );

        if ($validator->fails()) {
            $array['error'] = $validator->errors();
            return $this->jsonResponse($array, 400);
        }

        $name = $request->input(['name']);
        $email = $request->input(['email']);
        $birthdate = $request->input(['birthdate']);
        $city = $request->input(['city']);
        $work = $request->input(['work']);
        $password = $request->input(['password']);
        $password_confirm = $request->input(['password_confirm']);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $user = User::find($this->loggedUser['id']);

        if ($email && $user->email != $email) {
            $emailExist = User::where('email', $email)->count();
            if ($emailExist === 0) {
                $user->email = $email;
            } else {
                $array['error'] = 'E-mail já existe!';
                return $this->jsonResponse($array);
            }
        }

        if ($password){
            if($password === $password_confirm) {
                $user->password = $hash;
            } else {
                $array['error'] = 'As senhas não são iguais';
                return $this->jsonResponse($array);
            }
        }

        ($name) && $user->name = $name;
        ($birthdate) && $user->birthdate = $birthdate;
        ($city) && $user->city = $city;
        ($work) && $user->work = $work;
        $user->save();

        return $this->jsonResponse($array);
    }

    public function updateAvatar(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('avatar');

        if($image) {
            if(in_array($image->getClientMimeType(), $allowedTypes)){
                $fileName = md5(time().rand(0,9999)).'.jpg';
                $path = public_path('/media/avatars') ;
                $image = Image::make($image->path())->fit(200,200)->save($path.'/'.$fileName);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $fileName;
                $user->save();

                $array['url'] = url('/media/avatars/'.$fileName);
            } else {
                $array['error'] = 'Arquivo não suportado';
                return $this->jsonResponse($array);
            }
        } else {
            $array['error'] = 'Arquivo não enviado';
            return $this->jsonResponse($array);
        }

        return $this->jsonResponse($array);
    }

    public function updateCover(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('cover');

        if($image) {
            if(in_array($image->getClientMimeType(), $allowedTypes)){
                $fileName = md5(time().rand(0,9999)).'.jpg';
                $path = public_path('/media/covers') ;
                $image = Image::make($image->path())->fit(850,310)->save($path.'/'.$fileName);

                $user = User::find($this->loggedUser['id']);
                $user->cover = $fileName;
                $user->save();

                $array['url'] = url('/media/covers/'.$fileName);
            } else {
                $array['error'] = 'Arquivo não suportado';
                return $this->jsonResponse($array);
            }
        } else {
            $array['error'] = 'Arquivo não enviado';
            return $this->jsonResponse($array);
        }

        return $this->jsonResponse($array);
    }
}
