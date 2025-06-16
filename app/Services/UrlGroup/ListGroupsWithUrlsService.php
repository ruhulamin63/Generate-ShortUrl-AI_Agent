<?php 
declare(strict_types=1);
namespace App\Services\UrlGroup;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class ListGroupsWithUrlsService{


    public function execute(string $userID): array{
       $groupsWithUrls = $this->searchGroupWithUrls($userID);
       $data = $this->prepareGroupData($groupsWithUrls);
       return $data;
    }

    private function searchGroupWithUrls(string $userId): Collection{

 

        $groups = Group::where('user_id', $userId)
            ->select(['id', 'name', 'description','created_at']) // campos deseados del grupo
            ->with(['urls' => function ($query) {
                $query->select(['id', 'original_url', 'shortened_url', 'custom_alias', 'description', 'is_active', 'group_id']); // campos deseados de la URL
            }])
            ->get();
 return  $groups;
    }
    private function prepareGroupData($groupsWithUrls): array
    {
        return $groupsWithUrls->map(function ($group) {
            return [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_description' => $group->description,
                'created_at' => $group->created_at,
                'urls' => $group->urls->map(function ($url) {
                    return [
                        'url_id' => $url->id,
                        'original_url' => $url->original_url,
                        'shortened_url' => $url->shortened_url,
                        'custom_alias' => $url->custom_alias,
                        'url_description' => $url->description,
                        'is_active' => $url->is_active,
                    ];
                }),
            ];
        })->toArray();
    }
}