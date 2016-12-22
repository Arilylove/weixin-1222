<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/19
 * Time: 16:58
 */
namespace app\index\controller;
use think\Controller;
use app\index\controller\Index;
class Higher extends Controller{
    /*
     * 临时二维码获取
     * */
    public function getTmpCode(){
        //1.获取ticket票据
        //全局票据access_token,网页授权access_token,微信js-SDK jsapi-ticket
        $token=new Index();
        $access_token=$token->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        //数据{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr=array(
            'expire_seconds'=>604800,   //时限24*60*60*7；一周
            'action_name'=>'QR_SCENE',
            'action_info'=>array(
                "scene"=>array(
                    'scene_id'=>2000,
                    ),
                )
        );
        $postJson=json_encode($postArr);
        $res=$token->http_curl($url,'post','json',$postJson);
        $ticket=$res['ticket'];
        //2.ticket获取二维码图片
        $turl="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        return "<img src='".$turl."'/>";
        return '临时';
    }

    /*
     * 永久二维码获取
     * */
    public function getForCode(){
        //1.获取ticket票据
        //全局票据access_token,网页授权access_token,微信js-SDK jsapi-ticket
        $token=new Index();
        $access_token=$token->getWxAccessToken();
        $url=" https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        //数据{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr=array(
            'action_name'=>'QR_LIMIT_SCENE',
            'action_info'=>array(
                "scene"=>array(
                    'scene_id'=>3000,
                ),
            )
        );
        $postJson=json_encode($postArr);
        $res=$token->http_curl($url,'post','json',$postJson);
        $ticket=$res['ticket'];
        //2.ticket获取二维码图片
        $furl="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        return "<img src='".$furl."'/>";
        return '永久';
    }

    /*
     * 扫码推送
     * */
    public function codeResponse(){
        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];
        $postobj=simplexml_load_string($postArr);
        if(strtolower($postobj->MsgType)=='event') {
            //关注过，重扫二维码
            if (strtolower($postobj->Event) == 'scan') {
                 if(strtolower($postobj->EventKey)=='2000'){
                     //临时二维码
                     $content='欢迎关注临时二维码';
                 }else if(strtolower($postobj->EventKey)=='3000'){
                     //永久二维码
                     $content='欢迎关注永久二维码';
                 }
                $arr=array(          //不是必须；多图文则再添加几个数组内容
                    array(
                        'title'=>$content,
                        'description'=>'图文消息描述',
                        'picurl'=>'图片链接',
                        'url'=>'跳转链接'
                    ),
                    array(
                        'title'=>'图文消息标题',
                        'description'=>'图文消息描述',
                        'picurl'=>'图片链接',
                        'url'=>'跳转链接'
                    )      //。。。。
                );
                $template="<xml>
                <ToUserName><![CDATA[toUser]]></ToUserName>
                <FromUserName><![CDATA[fromUser]]></FromUserName>
                <CreateTime>12345678</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>".count($arr)."</ArticleCount>
                <Articles>";     //多图文拼接

                foreach ($arr as $k=>$v){
                    $template.="<item>
                <Title><![CDATA[".$v['title']."]]></Title> 
                <Description><![CDATA[".$v['description']."]]></Description>
                <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
                <Url><![CDATA[".$v['url']."]]></Url>
                </item>";
                }

                $template.="</Articles>
                        </xml>";
                $toUser=$postobj->ToUserName;
                $fromUser=$postobj->FromUserName;
                $time=time();
                $msgType='图文';
                // $content='纯文本消息回复'; //回复消息
                $info=sprintf($template,$fromUser,$toUser,$time,$msgType,$content);//按照模板的顺序写
                return $info;
            }
        }

    }




}