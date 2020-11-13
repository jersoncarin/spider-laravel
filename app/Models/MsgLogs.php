<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsgLogs extends Model
{
    use HasFactory;

    protected $fillable = ['content'];

    protected $table = 'msg_logs';

    protected $primaryKey = 'id';
}
