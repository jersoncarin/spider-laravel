<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'subject_id',
        'sender'
    ];

    protected $table = 'customer_chat_message';
    protected $primaryKey = 'id';
}
