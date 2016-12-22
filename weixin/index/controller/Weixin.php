<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/19
 * Time: 14:25
 */
namespace app\index\controller;
use think\Controller;
/*SDK*/
class Weixin extends Controller{
    //公用封装
    /*
     * 单文本回复
     * */
    public function responseText($postobj,$content){
        $template="<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>12345678</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[你好]]></Content>
            </xml>";
        $toUser=$postobj->ToUserName;
        $fromUser=$postobj->FromUserName;
        $time=time();
        $msgType='text';
        // $content='纯文本消息回复'; //回复消息
        $info=sprintf($template,$fromUser,$toUser,$time,$msgType,$content);//按照模板的顺序写
        return $info;
    }
    /*
     * 图文回复
     * */
    public function responsePic($postobj,$arr,$content){
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
    /*
     * 关注消息回复
     * */
    public function responseSubscribe($postobj,$arr){
        return $this->responseText($postobj,$arr);
    }
    
}