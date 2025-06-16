<?php 
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;

class RegisterUserController extends Controller
{
    public function __construct(
        private \App\Services\User\RegisterUserService $registerUserService,
    ) {
    }
    public function __invoke(RegisterUserRequest $request)
    
      
        // Create the user
    {
        $this->registerUserService->createUser(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            username: $request->username
        );
        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }
}