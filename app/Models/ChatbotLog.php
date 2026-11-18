<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    protected $fillable = [
        'session_id',
        'user_message',
        'bot_response',
        'provider_used',
        'model_used',
        'was_blocked_off_topic',
    ];

    protected $casts = [
        'was_blocked_off_topic' => 'boolean',
    ];
}
