<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/20
 * Time: 9:37
 */
namespace app\index\controller;
use think\Controller;
use app\index\controller\Index;
class Group extends Controller{
    /*
     * 群发接口，发送图文、语音等时需要media_id(上传时获得);
     * */
    public function sendMsgText(){
        //用预览接口进行测试，POST，https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN
        //1.获取全局access_token
        $token=new Index();
        $access_token=$token->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;
        //2.组装群发接口数据array,文本
           /* {
                "touser":"OPENID",
               "text":{
                 "content":"CONTENT"
                 },
               "msgtype":"text"
       }*/
        $array=array(
            'touser'=>'用户的open_id',
            'text'=>array(
                'content'=>'文本内容',
            ),
            'msgtype'=>'消息类型'
        );
        //3.将array->json
        $postJson=json_encode($array);
        //4.调用curl
        $res=$token->http_curl($url,'post','json',$postJson);
        var_dump($res);

    }
    /*
     * 图文消息
     * */
    public function sendMsgPic(){
        //用预览接口进行测试，POST，https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN
        //1.获取全局access_token
        $token=new Index();
        $access_token=$token->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;
        //2.组装群发接口数据array
            /* {
               "touser":"OPENID",
               "mpnews":{
                        "media_id":"123dsdajkasd231jhksad"
                         },
               "msgtype":"mpnews"
            }*/
        $array=array(
            'touser'=>'用户的open_id',
            'mpnews'=>array(
                'media_id'=>'上传图片获取：123dsdajkasd231jhksad',
            ),
            'msgtype'=>'消息类型'
        );
        //3.将array->json
        $postJson=json_encode($array);
        //4.调用curl
        $res=$token->http_curl($url,'post','json',$postJson);
        var_dump($res);

    }
}