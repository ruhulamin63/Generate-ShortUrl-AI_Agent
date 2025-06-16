<?php 
declare(strict_types=1);
namespace App\Services\Url;

use Illuminate\Database\Eloquent\Collection;

class ListAllUrlByUserService
{
    use UrlUtilsTrait;

    public function listAllUrlByUserId(string $userId): Collection | array{
        $urls = $this->listUrlsByUserId($userId);
        if (empty($urls)) {
            return [];
        }
        return $urls;
    }
}