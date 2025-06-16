<?php

namespace App\Http\Controllers\Api\v1\UrlGroup;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupFromUrlRequest;
use App\Services\UrlGroup\UnassignGroupFromUrlService;
use Illuminate\Http\Request;

class UnassignGroupFromUrlController extends Controller
{
    public function __construct(private UnassignGroupFromUrlService $unassignGroupFromUrlService){}

    public function __invoke(GroupFromUrlRequest $request) {
        $this->unassignGroupFromUrlService->execute($request->url_id, $request->group_id, $request->user()->id);
        return response()->json(['message' => 'Url unassigned from group successfully'], 200);
    }
}
