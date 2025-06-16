<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasUuids;
    protected $fillable = [
      'user_id',
      'group_id',
      'original_url',
      'shortened_url',
      'custom_alias',
      'password',
      'description',
      'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
