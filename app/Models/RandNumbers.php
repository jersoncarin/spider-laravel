<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RandNumbers extends Model
{
    use HasFactory;

    protected $fillable = ['account_name', 'account_number'];

    protected $table = 'rand_numbers';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
