<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WechatUser extends Model
{
    use HasFactory;
    protected $table = 'wechat_users';
    protected $guarded = [];
    protected $dateFormat = 'U';
}
