<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteLogs extends Model
{
    use HasFactory;

    protected $fillable = ['amount','fight_id'];

    protected $table = 'site_bet_logs';

    protected $primaryKey = 'id';
}
