<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasUuids;
    protected $fillable = [
'conversation_id',
      //  'user_id',
        'message',
        'sender',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
