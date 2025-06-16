<?php
declare(strict_types=1);
namespace App\Neuron\Tools\Url;

use App\Exceptions\UrlNotFoundException;
use App\Services\Url\DeleteUrlByShortCodeService;

class DeleteUrlTool
{
    private DeleteUrlByShortCodeService $deleteUrlByShortCodeService;
    
    public function __construct(DeleteUrlByShortCodeService $deleteUrlByShortCodeService)
    {
        $this->deleteUrlByShortCodeService = $deleteUrlByShortCodeService;
    }
    
    public function __invoke(string $shortenedUrl)
    {
        try {
            $this->deleteUrlByShortCodeService->deleteUrlByShortCodeAndUserId($shortenedUrl, request()->user()->id);
            return 'URL deleted successfully';
        } catch (UrlNotFoundException $e) {
            return 'Error deleting URL: ' . $e->getMessage();
        }
    }
}