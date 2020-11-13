<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetLogs extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'user_id','fight_id','side','fight_no','action','bet','balance'];

    protected $table = 'bet_logs';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
