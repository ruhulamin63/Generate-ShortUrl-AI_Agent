<?php 
namespace App\Services\Group;

use App\Exceptions\GroupNotFoundException;
use App\Models\Group;

trait FindGroupByUserTrait {
    public function findGroupById(string $userId, string $groupId) {
    $group =  Group::where('user_id', $userId)->where('id', $groupId)->first();
    if(!$group) {
        throw new GroupNotFoundException();
    }
    return $group;
    }
}