<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fights extends Model
{
    use HasFactory;

    protected $fillable = ['fight_status', 'betting_status','fight_declaration','id','fight_no'];

    protected $table = 'fights';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
