<?php
declare(strict_types=1);
namespace App\Neuron\Tools\Url;

use App\Models\Url;
use App\Neuron\Helper\Url\ValidateCreateShorteneUrlTrait;
use App\Services\Url\UpdateShortenUrlService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUrlInfoTool
{
    use ValidateCreateShorteneUrlTrait;
    use \App\Services\Url\UrlUtilsTrait;


    public function __invoke(string $shortenedUrl,?string $customAlias = null, ?string $description = null, ?string $password = null,  ?string $newShortenedUrl = null, ?string $originalUrl = null, ?string $groupId = null, ?bool $isActive = null)
    {

        $url = $this->findUrlByUserIdAndShortenedUrl(request()->user()->id, $shortenedUrl);
  
        if (!$url) {
            return 'URL not found';
        }


        if ($originalUrl && !$this->validateUrl($originalUrl)) {
            return 'Invalid URL';
        }
        if ($customAlias && !$this->validateCustomAlias($customAlias)) {
            return 'Invalid custom alias';
        }
        if ($description && !$this->validateDescription($description)) {
            return 'Invalid description';
        }
        if ($password && !$this->validatePassword($password)) {
            return 'Invalid password';
        }
        $this->updateUrl($customAlias, $description, $password, $shortenedUrl, $newShortenedUrl, $originalUrl, $groupId, $isActive, $url);
       



        /*  
        $this->shortenUrlService->updateUrl($originalUrl, $shortenedUrl, $customAlias, $password,$description,$url->id,$groupId,$isActive, request()->user()->id, ); */
    }

    private function updateUrl(?string $customAlias = null, ?string $description = null, ?string $password = null, string $shortenedUrl, ?string $newShortenedUrl = null, ?string $originalUrl = null, ?string $groupId = null, ?bool $isActive = null, Url $url)
    {
       
        if ($newShortenedUrl != null) {
            if ($this->isUrlExists($newShortenedUrl)) {
                return 'Shortened URL already exists';
            }
        }
      
     
      
            if ($originalUrl != null) {
                $url->original_url = $originalUrl;
            }
            $url->shortened_url = $newShortenUrl?? $url->shortened_url;
            if ($customAlias != null) {
                $url->custom_alias = $customAlias;
            }
            if ($password != null) {
                $url->password = Hash::make($password);
            }
            if ($description != null || $description != '') {
               
                $url->description = $description;
            }
            if ($groupId != null) {
                $url->group_id = $groupId;
            }
            if ($url->is_acive != null) {
                $url->is_active =  $isActive;
            }
            $url->save();
      
    }
}
