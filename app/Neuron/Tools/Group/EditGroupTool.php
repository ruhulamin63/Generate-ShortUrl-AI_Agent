<?php

namespace App\Neuron\Tools\Group;

use App\Exceptions\GroupNotFoundException;
use App\Services\Group\UpdateGroupService;

class EditGroupTool
{
    public function __construct(private UpdateGroupService $updateUrlGroupService)
    {
        // Constructor logic if needed
    }

    public function __invoke(string $groupId, ?string $groupName = null, ?string $description = null)
    {
        try {
            $this->updateUrlGroupService->execute($groupId, $groupName, $description, request()->user()->id); // Validate the input
        } catch (GroupNotFoundException $e) {
            // Handle the exception as needed
            return 'Group not found: ' . $e->getMessage();
        }
       
    }
}