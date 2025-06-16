<?php 
namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Services\User\ShowUserService;
use Illuminate\Http\Request;

class ShowUserController extends Controller
{
  

    public function __invoke(Request $request)
    {
        // Find the user by ID
        $user = $request->user();
        $data= ['name' => $user->name, 'email' => $user->email, 'username' => $user->username];

       

        // Return the user data
        return response()->json($data, 200);
    }
} 