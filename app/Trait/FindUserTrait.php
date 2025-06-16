<?php
declare(strict_types=1);
namespace App\Trait;

use App\Exceptions\UserNotFound;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;

trait FindUserTrait
{
    public function findUserById(string $userId): User
    {
        try{
            $user = User::find($userId);
            if (!$user) {
                throw new UserNotFound();
            }
            return $user;
        }catch(UserNotFound $e){
           throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
    }
}
