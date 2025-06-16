<?php
declare(strict_types=1);
namespace App\Neuron\Tools\Group;

class ListAllgroupsByUserTool
{
 
    
    public function __invoke()
    {
        $groups = request()->user()->groups()->get();
        if ($groups->isEmpty()) {
            return [];
        }
        return $groups;
    }
}