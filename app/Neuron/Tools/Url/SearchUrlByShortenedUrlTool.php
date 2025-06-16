<?php
declare(strict_types=1);
namespace App\Neuron\Tools\Url;

use App\Exceptions\UrlNotFoundException;
use App\Services\Url\UrlUtilsTrait;


class SearchUrlByShortenedUrlTool  {
    use UrlUtilsTrait;
    public function __construct(){
   
    }
    public function __invoke(string $shortened_url)
    {
        $shortCode = $this->findUrlByUserIdAndShortenedUrl(request()->user()->id,$shortened_url);
        if ($shortCode === null) {
           return '';
        }
        $data=[
            'shortened_url' => $shortCode->shortened_url,
            'original_url' => $shortCode->original_url,
            'custom_alias' => $shortCode->custom_alias,
            'description' => $shortCode->description,
            'is_active' => $shortCode->is_active,
            'id' => $shortCode->id
        ];
        return $data;
    }
}
