<?php 
declare(strict_types=1);
namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserService{

    public function deleteUser(User $user): void{
        DB::transaction(function () use ($user) {
            $user->urls()->delete();
        });
        }
}