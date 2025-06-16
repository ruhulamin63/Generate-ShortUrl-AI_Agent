<?php 
declare(strict_types=1);
namespace App\Services\Group;

use App\Exceptions\GroupNotFoundException;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
class DeleteGroupService{
use FindGroupByUserTrait;
    public function execute(string $groupId, string $userId) {
        $group = $this->findGroupById($groupId, $userId);
       
        DB::transaction(function () use ($group) {
            $group->delete();
        });
       
    }
}