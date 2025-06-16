<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\UrlGroup;

use App\Http\Controllers\Controller;
use App\Services\UrlGroup\ListGroupsWithUrlsService;
use Illuminate\Http\Request;

class ListGroupsWithUrlsController extends Controller
{
    public function __construct(private ListGroupsWithUrlsService $listGroupsWithUrlsService){}

    public function __invoke(Request $request){
        $userID = $request->user()->id;
        $groupsWithUrls = $this->listGroupsWithUrlsService->execute($userID);
        return response()->json($groupsWithUrls);
    }
}
