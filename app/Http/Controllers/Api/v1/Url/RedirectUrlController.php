<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\Url;

use App\Exceptions\UrlNotFoundException;
use App\Exceptions\UrlPasswordInvalidException;
use App\Http\Controllers\Controller;
use App\Services\Url\RedirectUrlService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class RedirectUrlController extends Controller
{
    public function __construct(private RedirectUrlService $redirectUrlService){}
    public function __invoke(Request $request)
    {
        $shortUrl = $request->route('shortUrl');
        try {
            $url = $this->redirectUrlService->getOriginalUrl($shortUrl, $request->password ?? null, $request->ip(), $request->userAgent(), '$referrer');

            return redirect()->away($url);
        }
        catch (UrlPasswordInvalidException | UrlNotFoundException $e){
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
    }
}
