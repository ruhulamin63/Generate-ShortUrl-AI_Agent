<?php 
declare(strict_types=1);
namespace App\Services\Url;
use App\Exceptions\ShortUrlExistException;
use App\Models\Url;
use Illuminate\Database\Eloquent\Collection;

trait UrlUtilsTrait{
     
  public function generateShortenedUrl(): string
  {
    $length = 6;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $maxIndex = strlen($characters) - 1;

    do {
      $shortenedUrl = '';
      for ($i = 0; $i < $length; $i++) {
        $shortenedUrl .= $characters[random_int(0, $maxIndex)];
      }
    } while ($this->isUrlExists($shortenedUrl));

    return $shortenedUrl;
  }


  public function isUrlExists(string $shortenedUrl): bool
  {
    return Url::where('shortened_url', $shortenedUrl)->exists();
  }

  public function findUrlByUrlIdAndUserId(string $urlId, string $userId): ?Url
  {
    return Url::where('user_id', $userId)->where('id', $urlId)->first();
  }
  
  public function findUrlByUserIdAndShortenedUrl(string $userId, string $shortenedUrl): ?Url{
    return Url::where('user_id', $userId)->where('shortened_url', $shortenedUrl)->first();
  }

    public function findUrlByShortenedUrl(string $shortenedUrl): ?Url
    {
        return Url::where('shortened_url', $shortenedUrl)->first();
    }

    private function listUrlsByUserId(string $userId): Collection
    {
        return Url::where('user_id', $userId)->get()->select('id','shortened_url', 'original_url','custom_alias','description','is_active');
    }

    
    
}