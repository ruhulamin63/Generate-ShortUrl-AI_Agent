<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\Url;

use App\Exceptions\UrlNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Url\DeleteUrlByIdService;
use App\Services\Url\DeleteUrlByShortCodeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class DeleteShortUrlcontroller extends Controller
{
    public function __construct(private DeleteUrlByShortCodeService $deleteUrlByShortCodeService){}

    public function __invoke(Request $request, string $shortUrl)
    {
        try {
        $this->deleteUrlByShortCodeService->deleteUrlByShortCodeAndUserId($shortUrl, $request->user()->id);

        return response()->json([
            'message' => 'Short URL deleted successfully',
        ], 200);
    } catch (UrlNotFoundException $e) {
        throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
    }
    }
}
