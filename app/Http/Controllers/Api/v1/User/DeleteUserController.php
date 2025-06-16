<?php 

namespace App\Http\Controllers\Api\v1\User;
use App\Http\Controllers\Controller;
use App\Services\User\DeleteUserService;
use Illuminate\Http\Request;


class DeleteUserController extends Controller
{
    public function __construct(private DeleteUserService $deleteUserService){}
    public function __invoke(Request $request)
    {
        // Logic to delete the user
        // For example, you might want to call a service to handle the deletion
         $this->deleteUserService->deleteUser($request->user());

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }
}