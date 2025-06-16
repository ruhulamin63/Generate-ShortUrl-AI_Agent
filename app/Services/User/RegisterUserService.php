<?php 
declare(strict_types=1);
namespace App\Services\User;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserService{
    public function createUser(string $name, string $email, string $password, string $username): void{

        DB::transaction(function() use ($name, $email, $password, $username) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'username' => $username,
            ]);
            $user->assignRole('user');
        });
    }
}