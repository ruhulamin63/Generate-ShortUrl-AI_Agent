<?php 
declare(strict_types=1);
namespace App\Neuron\Tools\GroupFromUrl;

use App\Exceptions\GroupNotFoundException;
use App\Exceptions\UrlNotFoundException;
use App\Services\UrlGroup\AssignGroupFromUrlService;

class AssignGroupFromUrlTool
{
    public function __construct(private AssignGroupFromUrlService $assignUrlToGroupService) {}
    public function __invoke(string $groupId, string $shortenedUrl)
    {
        // Validate the input
        if (empty($groupId) || empty($shortenedUrl)) {
            return 'Group ID and URL ID cannot be empty';
        }

        // Assuming you have a service to handle the assignment logic
        try{
            $this->assignUrlToGroupService->execute($groupId, $shortenedUrl, request()->user()->id);
        }catch (GroupNotFoundException | UrlNotFoundException $e) {
            // Handle the exception as needed
            return 'Group or URL not found: ' . $e->getMessage();
        }
        
    
    }
}