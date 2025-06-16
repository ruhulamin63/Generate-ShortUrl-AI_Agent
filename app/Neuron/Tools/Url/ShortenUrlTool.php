<?php
declare(strict_types=1);
namespace App\Neuron\Tools\Url;

use App\Neuron\Helper\Url\ValidateCreateShorteneUrlTrait;
use App\Services\Url\ShortenUrlService;
use App\Services\Url\UrlUtilsTrait;

class ShortenUrlTool
{
    use UrlUtilsTrait;
    use ValidateCreateShorteneUrlTrait;
    public function __construct(private ShortenUrlService $shortenUrlService){}
    public function __invoke( string $originalUrl, ?string $customAlias = null, ?string $description = null, ?string $password =null, ?string $shortenedUrl = null)
    {

        if (!$this->validateUrl($originalUrl)) {
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
        
        
        
        // Check if the shortened URL already exists
    
        if($shortenedUrl){
        $url = $this->findUrlByShortenedUrl($shortenedUrl);
        if ($url){
            return 'Shortened URL already exists';
        }
    } 
       
        $this->shortenUrlService->shortenUrl($originalUrl, $shortenedUrl, $customAlias, $password, $description, request()->user()->id);
    
}
}