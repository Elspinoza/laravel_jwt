<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class AuthController extends BaseController
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);


        $success1['user'] =  $user;

        return $this->sendResponse($success1, 'User register successfully.');
    }

    public function login()
    {

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized.']);
        }

        $success = $this->respondWithToken($token);

        return $this->sendResponse($success, 'User login successfully.');
    }


    public function profile()
    {
//        $success = $this->respondWithToken(auth()->user());

        $success = auth()->user();

        return $this->sendResponse($success, 'User profile successfully.');
    }

    public function logout()
    {
        $success = auth()->logout();
        return $this->sendResponse($success, 'User logout successfully.');
    }

    public function refresh()
    {
        $success = $this->respondWithToken(auth()->refresh());
        return $this->sendResponse($success, 'User refresh successfully.');
    }


    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }


}
