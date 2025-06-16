<?php
declare(strict_types=1);
namespace App\Services\Url;

use App\Exceptions\UrlNotFoundException;
use App\Models\Url;
use Illuminate\Support\Facades\DB;

class DeleteUrlByShortCodeService{
use UrlUtilsTrait;

    public function deleteUrlByShortCodeAndUserId(string $shortUrl,string $userId): void
    {
       $url= $this->findUrlByUserIdAndShortenedUrl($userId, $shortUrl);
       if(!$url){
        throw new UrlNotFoundException();
       }
       DB::transaction(function () use ($url) {
        $url->delete();
       });
    }
}