<?php
declare(strict_types=1);
namespace App\Neuron\Tools\Url;

class ListAllUrlByUser
{
    public function __construct(private \App\Services\Url\ListAllUrlByUserService $listAllUrlByUserService)
    {
        // Constructor logic if needed
    }
    
    public function __invoke()
    {
        return $this->listAllUrlByUserService->listAllUrlByUserId(request()->user()->id);
    }
}