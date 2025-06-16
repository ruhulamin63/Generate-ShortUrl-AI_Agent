<?php 
declare(strict_types=1);    
namespace App\Neuron\Tools\GroupFromUrl;

use App\Exceptions\GroupNotFoundException;
use App\Services\UrlGroup\UnAssignGroupFromUrlService;
use App\Exceptions\UrlGroupNotFoundException;
use App\Exceptions\UrlNotFoundException;

class UnAssignGroupFromUrlTool
{
    public function __construct(private UnAssignGroupFromUrlService $unAssignGroupFromUrlService)
    {
        // Constructor logic if needed
    }
    public function __invoke(string $groupId, string $shortenedUrl): string
    {
        try {
            $this->unAssignGroupFromUrlService->execute($shortenedUrl, $groupId, request()->user()->id);
            return 'URL unassigned from group successfully';
        } catch (UrlNotFoundException | GroupNotFoundException  $e) {
            return 'Error unassigning URL from group: ' . $e->getMessage();
        }
    }
}