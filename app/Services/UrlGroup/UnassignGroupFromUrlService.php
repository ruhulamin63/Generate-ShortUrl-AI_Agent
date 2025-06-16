<?php 
declare(strict_types=1);
namespace App\Services\UrlGroup;

use App\Services\Group\FindGroupByUserTrait;
use App\Services\Url\UrlUtilsTrait;
use Illuminate\Support\Facades\DB;

class UnassignGroupFromUrlService{
    use FindGroupByUserTrait;
    use UrlUtilsTrait;

    public function execute(string $shortenedUrl, string $groupId, string $userId) {
        $group = $this->findGroupById($userId, $groupId);
        $url = $this->findUrlByUserIdAndShortenedUrl($userId, $shortenedUrl);
        DB::transaction(function () use ($url, ) {
            $url->group_id = null;
            $url->save();
        });
      
    }

}