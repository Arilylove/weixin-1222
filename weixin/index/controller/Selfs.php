<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/21
 * Time: 15:11
 */
namespace app\index\controller;
use think\Controller;
use app\index\controller\Index;
class Selfs extends Controller{
    /*
     * 自定义菜单
     * */
    public function getDefineMenu(){
        /*
         *click + view 请求
         * {
             "button":[
             {
                  "type":"click",
                  "name":"今日歌曲",
                  "key":"V1001_TODAY_MUSIC"
              },
              {
                   "name":"菜单",
                   "sub_button":[
                   {
                       "type":"view",
                       "name":"搜索",
                       "url":"http://www.soso.com/"
                    },
                    {
                       "type":"view",
                       "name":"视频",
                       "url":"http://v.qq.com/"
                    },
                    {
                       "type":"click",
                       "name":"赞一下我们",
                       "key":"V1001_GOOD"
                    }]
               }]
         }
         * */
        /*
         * 其他按钮类型请求
         *{
            "button": [
                {
                    "name": "扫码",
                    "sub_button": [
                        {
                            "type": "scancode_waitmsg",
                            "name": "扫码带提示",
                            "key": "rselfmenu_0_0",
                            "sub_button": [ ]
                        },
                        {
                            "type": "scancode_push",
                            "name": "扫码推事件",
                            "key": "rselfmenu_0_1",
                            "sub_button": [ ]
                        }
                    ]
                },
                {
                    "name": "发图",
                    "sub_button": [
                        {
                            "type": "pic_sysphoto",
                            "name": "系统拍照发图",
                            "key": "rselfmenu_1_0",
                           "sub_button": [ ]
                         },
                        {
                            "type": "pic_photo_or_album",
                            "name": "拍照或者相册发图",
                            "key": "rselfmenu_1_1",
                            "sub_button": [ ]
                        },
                        {
                            "type": "pic_weixin",
                            "name": "微信相册发图",
                            "key": "rselfmenu_1_2",
                            "sub_button": [ ]
                        }
                    ]
                },
                {
                    "name": "发送位置",
                    "type": "location_select",
                    "key": "rselfmenu_2_0"
                },
                {
                   "type": "media_id",
                   "name": "图片",
                   "media_id": "MEDIA_ID1"
                },
                {
                   "type": "view_limited",
                   "name": "图文消息",
                   "media_id": "MEDIA_ID2"
                }
            ]
        }
                 * */
        $token=new Index();
        $access_token=$token->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $postArr=array(
            'button'=>array(
                array('type'=>'click',
                    'name'=>urlencode('今日歌曲'),
                    'key'=>'V1001_TODAY_MUSIC'
                ),
                array(
                    'name'=>urlencode('扫码'),
                    'sub_button'=>array(
                       array(
                           "type"=>"view",
                           "name"=>urlencode("搜索"),
                           "url"=>"http://www.soso.com/"
                       ),
                        array(
                            "type"=>"view",
                            "name"=>urlencode("视频"),
                            "url"=>"http://v.qq.com/"
                        )
                    ),
                ),
                array(
                    "type"=>"click",
                    "name"=>urlencode("赞一下我们"),
                    "key"=>"V1001_GOOD"
                )
            )
        );
        $postJson=json_encode($postArr);
        $res=$token->http_curl($url,'post','json',$postJson);
        var_dump($res);
    }
    /*
     * 事件推送
     * */
    public function pushMsg(){
        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];
        $postobj=simplexml_load_string($postArr);
        $weixin=new Weixin();
        if(strtolower($postobj->MsgType)=='event'){
            if(strtolower($postobj->Event)=='click'){
                if(strtolower($postobj->EventKey)=='V1001_TODAY_MUSIC'){
                    $content='click类型歌曲事件推送';
                    $res=$weixin->responseSubscribe($postobj,$content);
                    return $res;
                }
                $content='click类型事件推送';
                $res=$weixin->responseSubscribe($postobj,$content);
                return $res;
            }else if(strtolower($postobj->Event)=='view'){
                $content='view类型事件推送，跳转链接是：'.$postobj->EventKey;
                $res=$weixin->responseSubscribe($postobj,$content);
                return $res;
            }
        }
    }


}
