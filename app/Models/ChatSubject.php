<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_name',
        'user_id',
        'hasReply',
        'status'
    ];

    protected $table = 'customer_chat_subject';
    protected $primaryKey = 'id';
}
