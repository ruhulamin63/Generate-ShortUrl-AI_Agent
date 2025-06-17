<?php
namespace App\Services\UrlGroup;

use App\Exceptions\UrlNotFoundException;
use App\Services\Group\FindGroupByUserTrait;
use App\Services\Url\UrlUtilsTrait;
use Illuminate\Support\Facades\DB;

class AssignGroupFromUrlService{
    use FindGroupByUserTrait;
    use UrlUtilsTrait;
    public function execute(string $groupId, string $shortenedUrl, string $userId): void{
        $group = $this->findGroupById($userId, $groupId);
        $url = $this->findUrlByUserIdAndShortenedUrl($userId, $shortenedUrl);
        
        if (!$url) {
            throw new UrlNotFoundException();
        }
        DB::transaction(function () use ($url, $group) {
            $url->group_id = $group->id;
            $url->save();
        });
      
    }
}