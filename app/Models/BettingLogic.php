<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BettingLogic extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'user_id','fight_id','side','id'];

    protected $table = 'betting_logic';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
