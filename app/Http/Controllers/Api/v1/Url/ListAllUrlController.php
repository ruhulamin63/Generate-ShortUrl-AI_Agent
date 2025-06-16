<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\Url;

use App\Http\Controllers\Controller;
use App\Services\Url\ListAllUrlByUserService;
use Illuminate\Http\Request;

class ListAllUrlController extends Controller
{
    
    public function __construct(private ListAllUrlByUserService $listAllUrlService){}

    public function __invoke(Request $request) {
        return $this->listAllUrlService->listAllUrlByUserId($request->user()->id);
    }
}
