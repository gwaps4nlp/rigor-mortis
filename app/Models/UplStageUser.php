<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UplStageUser extends Model
{
    protected $fillable = ['upl_stage_id','user_id','experience','money','samples'];

    protected $table = 'upl_stage_user';
}
