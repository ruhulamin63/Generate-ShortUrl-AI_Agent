<?php 
namespace App\Services\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUrlGroupService {

    public function execute(string $userId, string $name, ?string $description) {
        DB::transaction(function () use ($userId, $name, $description) {
             Group::create([
           'user_id' => $userId,
           'name' => $name,
           'description' => $description??''
       ]);
        });
     
    }
}