<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'account_number','account_name','sender_number','contact_number','reciever_number','type','reference_number','screenshot_path','status'];

    protected $table = 'requests';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
