<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'session_id',
        'sender',
        'message',
        'is_read'
    ];
}
