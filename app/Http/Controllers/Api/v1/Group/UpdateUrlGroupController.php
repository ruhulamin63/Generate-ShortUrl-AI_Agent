<?php

namespace App\Http\Controllers\Api\v1\Group;

use App\Exceptions\GroupNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGroupRequest;
use App\Services\Group\UpdateGroupService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UpdateUrlGroupController extends Controller
{
    public function __construct(private UpdateGroupService $updateGroupService){}

    public function __invoke(UpdateGroupRequest $request, string $groupId) {
        try{
        $updatedGroup = $this->updateGroupService->execute($groupId, $request->name, $request->description, $request->user()->id);
        return response()->json([
            'message' => 'Group updated successfully',
            'group' => $updatedGroup
        ], 200);
    }
    catch(GroupNotFoundException $e){
        throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
    }
}
}
