<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class wechatController extends Controller
{
    function officialAccountServe(Request $req)
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注 都抖逗斗";
        });
        return $app->server->serve();
    }
}
