<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Resources\Api\UserResource;

class AuthController extends BaseController
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|max:255|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->respondError($validator->errors(), 422);
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);
            return $this->respond(new UserResource($user));
        } catch (Exception $e) {
            $message = 'Oops! Unable to create a new user.';
            return $this->respondError($message, 500);
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (!auth()->attempt($data)) {
            $message = ['error' => 'Unauthorised'];
            return $this->respondError($message, 401);
        }
        $user = auth()->user();
        return $this->respond(new UserResource($user));
    }
}
