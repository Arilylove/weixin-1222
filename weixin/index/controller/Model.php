<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/20
 * Time: 10:36
 */
namespace app\index\controller;
use think\Controller;
use app\index\controller\Index;
class Model extends Controller{
    /*
     * 模板消息发送接口
     * */
    public function sendMsg(){
        //https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN,POST
        /*
         *       {
           "touser":"OPENID",
           "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
           "url":"http://weixin.qq.com/download",                        //url（跳转链接）非必须
           "data":{
                   "first": {
                       "value":"恭喜你购买成功！",
                       "color":"#173177"
                   },
                   "keynote1":{
                       "value":"巧克力",
                       "color":"#173177"
                   },
                   "keynote2": {
                       "value":"39.8元",
                       "color":"#173177"
                   },
                   "keynote3": {
                       "value":"2014年9月22日",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"欢迎再次购买！",
                       "color":"#173177"
                   }
           }
       }
         * */
        $token=new Index();
        $access_token=$token->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $array=array(
            'touser'=>'open_id',
            'template_id'=>'模板id，添加之后生成',
            'url'=>'http://weixin.qq.com/download',
            'data'=>array(
                'first'=>array(
                    'value'=>'恭喜你购买成功！',
                    'color'=>'#173177'
                ),
                'second'=>array(
                    'value'=>'巧克力',
                    'color'=>'#173177'
                ),
                'third'=>array(
                    'value'=>'39.8元',
                    'color'=>'#173177'
                ),
            ),
        );
        $postJson=json_encode($array);
        $res=$token->http_curl($url,'post','json',$postJson);
        var_dump($res);
    }
}