<?php 
declare(strict_types=1);
namespace App\Services\Url;
use App\Exceptions\ShortUrlExistException;
use App\Exceptions\UrlNotFoundException;
use App\Exceptions\UrlPasswordInvalidException;
use App\Services\Click\ClickService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RedirectUrlService
{
    use UrlUtilsTrait;

    public function __construct(private ClickService $clickService){}
 

    public function getOriginalUrl(string $shortenedUrl, ?string $password,$ipAddress, $userAgent, $referrer): string
    {
        $url = $this->findUrlByShortenedUrl($shortenedUrl);
        
        if (!$url) {
            throw new UrlNotFoundException();
        }
        if ($url->password && !Hash::check($password, $url->password)) {
            throw new UrlPasswordInvalidException();
        }
        $this->clickService->createClick($url->id, $ipAddress, $userAgent, $referrer);
        
        return $url->original_url;
    }
}