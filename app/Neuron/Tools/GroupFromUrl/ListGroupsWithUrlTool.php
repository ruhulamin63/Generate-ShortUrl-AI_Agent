<?php 
declare(strict_types=1);
namespace App\Neuron\Tools\GroupFromUrl;

use App\Services\UrlGroup\ListGroupsWithUrlsService;

class ListGroupsWithUrlTool
{

    public function __construct(private ListGroupsWithUrlsService $listGroupsWithUrlsService)
    {
        // Constructor logic if needed
    }
    public function __invoke()
    {
        $userId = request()->user()->id;
        $groups = $this->listGroupsWithUrlsService->execute($userId);
        if (empty($groups)) {
            return [];
        }
        return $groups;
    }
}