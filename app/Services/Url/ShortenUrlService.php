<?php

declare(strict_types=1);

namespace App\Services\Url;

use App\Exceptions\ShortUrlExistException;
use App\Models\Url;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ShortenUrlService
{
  use UrlUtilsTrait;
  public function shortenUrl(string $originalUrl, ?string $shortenedUrl = null, ?string $customAlias = 'default', ?string $password = null, ?string $description = null, string $userId, string $groupId = null): Url
  {
    if($shortenedUrl){
      if ($this->isUrlExists($shortenedUrl)) {
        throw new ShortUrlExistException();
      }
    }

    if (!$shortenedUrl) {
      $shortenedUrl = $this->generateShortenedUrl();
    }
   $url= DB::transaction(function () use ($originalUrl, $shortenedUrl, $customAlias, $password, $description, $userId) {
         $transaction = Url::create([
      'user_id' => $userId,
      'original_url' => $originalUrl,
      'shortened_url' => $shortenedUrl,
      'custom_alias' => $customAlias,
      'password' => $password ?Hash::make($password) : null,
      'description' => $description,
    ]);

    return $transaction;
     
    });
    
   
   

    return $url;
  }
 
}
