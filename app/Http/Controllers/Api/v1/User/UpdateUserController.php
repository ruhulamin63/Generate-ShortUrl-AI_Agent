<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Services\User\UpdateUserService;
use Illuminate\Http\Request;

class UpdateUserController extends Controller
{
       public function __construct(
        private UpdateUserService $updateUserService,
    ) {
    }

    public function __invoke(UpdateUserRequest $request)
    {
        // Update the user
       $user = $this->updateUserService->updateUser(
            user: $request->user(),
            name: $request->name,
            email: $request->email,
            username: $request->username
        );
        return response()->json([
            'message' => 'User updated successfully', 'user' => $user
        ], 200);
    }
}
