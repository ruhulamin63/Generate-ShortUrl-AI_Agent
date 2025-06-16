<?php 
declare(strict_types=1);
namespace App\Services\Group;

use App\Exceptions\GroupNotFoundException;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class UpdateGroupService{
use FindGroupByUserTrait;
    public function execute(string $groupId, ?string $name, ?string $description, string $userId) {

        $group = $this->findGroupById($userId, $groupId);
    DB::transaction(function () use ($group, $name, $description) {
        if($name != null){
            $group->name = $name;
        }
        if($description != null){
            $group->description = $description;
        }
        $group->save();
    });

        return $group;
    }
}