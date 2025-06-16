<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    use HasUuids;
    protected $fillable = [
        'url_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'latitude',
        'longitude',
    ];

    public function url()
    {
        return $this->belongsTo(Url::class);
    }

 
}
