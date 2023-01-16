<?php

namespace App\Http\Controllers\Api;


use App\Models\User;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Resources\Api\UserResource;
class UserController extends BaseController
{
    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth()->user();
        
        return $this->respond((new UserResource($user))->withToken(true));
    }
    public function getAllUsers()
    {
        return $this->respond(UserResource::collection(User::orderBy('name')->get()));
    }
}
