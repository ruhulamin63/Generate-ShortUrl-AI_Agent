<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\Group;

use App\Exceptions\GroupNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Group\DeleteGroupService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class DeleteUrlGroupController extends Controller
{
    public function __construct(private DeleteGroupService $deleteGroupService){}

    public function __invoke(Request $request, string $groupId) {

        try{
              $this->deleteGroupService->execute($groupId, $request->user()->id);
              return response()->json(['message' => 'Group deleted successfully'], 200);
        }catch(GroupNotFoundException $e){
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
      
    }
}
