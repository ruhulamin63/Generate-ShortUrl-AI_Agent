<?php 
declare(strict_types=1);
namespace App\Neuron\Tools\Group;

use App\Services\Group\CreateUrlGroupService;

class CreateGroupTool{

    public function __construct(private CreateUrlGroupService $createUrlGroupService)
    {
        // Constructor logic if needed
    }
    public function __invoke(string $groupName, ?string $description = null)
    {
       $this->createUrlGroupService->execute(request()->user()->id, $groupName, $description); // Validate the input
      
    }
}