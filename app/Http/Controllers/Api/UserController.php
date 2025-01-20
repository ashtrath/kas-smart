<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();

        return $this->sendResponse('Users retrieved successfully.', $user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());

        return $this->sendResponse('User created successfully.', $user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (! $user) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse('User retrieved successfully.', $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if (! $user) {
            return $this->sendError('User not found.');
        }

        $user->update($request->all());

        return $this->sendResponse('User updated successfully.', $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (! $user) {
            return $this->sendError('User not found.');
        }

        $user->delete();

        return $this->sendResponse('User deleted successfully.', null, 204);
    }
}
