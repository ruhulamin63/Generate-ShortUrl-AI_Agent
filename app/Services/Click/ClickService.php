<?php 

namespace App\Services\Click;
use App\Models\Click;    
use App\Models\Url;
use Illuminate\Support\Facades\Http;

class ClickService{
    public function createClick(string $urlId, string $ipAddress, string $userAgent, string $referrer, )
    {
        $data=$this->track($ipAddress);
        // Create a new click record
        return Click::create([
            'url_id' => $urlId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'country' => $data['country'],
            'region' => $data['region'],
           
        ]);
    }

    private function track(string $ipAddress)
    {
        $ip = $ipAddress;
    $location = Http::get("http://ip-api.com/json/24.48.0.1");

    $country = $location['country'] ?? null;
    $city = $location['regionName'] ?? null;
    
    $latitude = $location['lat'] ?? null;
    $longitude = $location['lon'] ?? null;
    return [
        'country' => $country,
        'region' => $city,
        'latitude' => $latitude,
        'longitude' => $longitude,
    ];

    }
}