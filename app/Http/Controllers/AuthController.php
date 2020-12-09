<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __constructor()
    {
        $this->middleware('auth:api',
            ['except' => ['login', 'create', 'unauthorized']]
        );
    }

    public function create(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator($request->only(['name', 'email', 'password', 'birthdate']),
        [
            'name' => ['required','string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'birthdate' => ['required', 'date']
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors();
            return $this->jsonResponse($array, 400);
        }

        $data = $request->only(['name', 'email', 'password', 'birthdate']);

        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $birthdate = $data['birthdate'];

        $newUser = new User();
        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->password = $hash;
        $newUser->birthdate = $birthdate;
        $newUser->save();

        $token = auth()->attempt([
            'email' => $email,
            'password' => $password
        ]);

        if (!$token) {
            $array['error'] = 'Ocorreu um erro !';
            return $this->jsonResponse($array, 500);
        } else {
            $array['token'] = $token;
            return $this->jsonResponse($array);
        }
    }
}
