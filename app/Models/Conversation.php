<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasUuids;
    protected $fillable = [
        'user_id',
        'message',
        'started_at',
        'ended_at',
    ];

    


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
