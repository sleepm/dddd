<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WechatUser;
use App\Models\Todo;

class wechatController extends Controller
{
    function officialAccountServe(Request $req)
    {
        $app = app('wechat.official_account');
        $app->server->push(function($msg){
            $wu = WechatUser::firstOrNew(['openid'=>$msg['FromUserName']]);
            $wu->save();
            $msg['FromUserName'];
            $msg['CreateTime'];
            $msg['Content'];
            switch ($msg['MsgType']) {
                case 'text':
                    $todo = new Todo;
                    $todo->wechat_user_id = $wu->id;
                    $todo->to_user_name   = $msg['ToUserName'];
                    $todo->type           = 'text';
                    $todo->content        = $msg['Content'];
                    $todo->save();
                    return '200';
                case 'voice':
                case 'image':
                case 'video':
                    $todo = new Todo;
                    $todo->wechat_user_id = $wu->id;
                    $todo->to_user_name   = $msg['ToUserName'];
                    $todo->type           = $msg['MsgType'];
                    $file = [];
                    $stream = $app->media->get($msg['MediaId']);
                    if($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse){
                        $filename = $stream->saveAs(storage('app/media/'.$msg['ToUserName'].'/'));
                        $file []= $filename;
                    }
                    if($todo->type == 'video'){
                        $stream = $app->media->get($msg['ThumbMediaId']);
                        if($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse){
                            $filename = $stream->saveAs(storage('app/media/'.$msg['ToUserName'].'/'));
                            $file []= $filename;
                        }
                    }
                    $todo->file = $file;
                    if($todo->type == 'voice' && !empty($msg['Recognition'])){
                        $todo->content = $msg['Recognition'];
                    }
                    $todo->save();
                    return '200';
                case 'link':
                    $todo = new Todo;
                    $todo->wechat_user_id = $wu->id;
                    $todo->to_user_name   = $msg['ToUserName'];
                    $todo->type           = 'link';
                    $todo->content        = '标题：' . $msg['Title'] . '，URL：' . $msg['Url'];
                    $todo->save();
                    return '200';
                case 'location':
                    $todo = new Todo;
                    $todo->wechat_user_id = $wu->id;
                    $todo->to_user_name   = $msg['ToUserName'];
                    $todo->type           = 'location';
                    $todo->content        = '位置：' . $msg['Label'] . '，经度：' . $msg['Location_Y'] . '，纬度：' . $msg['Location_X'];
                    $todo->save();
                    return '200';
                default:
                    $todo = new Todo;
                    $todo->wechat_user_id = $wu->id;
                    $todo->to_user_name   = $msg['ToUserName'];
                    $todo->type           = $msg['MsgType'];
                    $todo->content        = json_encode($msg);
                    $todo->save();
                    return '200';
            }
        });
        return $app->server->serve();
    }
}
