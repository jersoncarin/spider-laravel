<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = ['master_agent_id', 'agent_id', 'code'];

    protected $table = 'referral';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
