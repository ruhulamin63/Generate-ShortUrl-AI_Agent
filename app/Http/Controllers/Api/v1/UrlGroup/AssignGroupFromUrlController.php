<?php

namespace App\Http\Controllers\Api\v1\UrlGroup;

use App\Exceptions\GroupNotFoundException;
use App\Exceptions\UrlNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupFromUrlRequest;
use App\Services\UrlGroup\AssignGroupFromUrlService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class AssignGroupFromUrlController extends Controller
{
    public function __construct(private AssignGroupFromUrlService $assignGroupToUrlService){}

    public function __invoke(GroupFromUrlRequest $request) {
        try{
         $this->assignGroupToUrlService->execute($request->group_id, $request->url_id, $request->user()->id);
            return response()->json(['message' => 'Url assigned to group successfully'], 200);
        }catch( GroupNotFoundException | UrlNotFoundException  $e){
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
    }
}
