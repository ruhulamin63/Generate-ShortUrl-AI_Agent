<?php

namespace App\Http\Controllers\Api\v1\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUrlGroupRequest;
use App\Services\Group\CreateUrlGroupService;
use Illuminate\Http\Request;

class CreateUrlGroupController extends Controller
{
    public function __construct(private CreateUrlGroupService $createUrlGroupService){}

    public function __invoke(CreateUrlGroupRequest $request) {
         $this->createUrlGroupService->execute($request->user()->id, $request->name, $request->description);

         return response()->json(['message' => 'Group created successfully'], 201);
    }
}
