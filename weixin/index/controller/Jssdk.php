<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/21
 * Time: 10:02
 */
namespace app\index\controller;
use think\Controller;
use app\index\controller\Index;
class Jssdk extends Controller{
    /*
     * 微信Js-SDK
     * */
    public function share(){

        $timestamp=time();
        $nonceStr=$this->getRandCode();
        $jsapi_ticket=$this->getJsapiTicket();
        $url=".../share";
        $signature="jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;
        $signature=sha1($signature);
        $this->assign('time',$timestamp);
        $this->assign('nonceStr',$nonceStr);
        $this->assign('signature',$signature);
        return $this->redirect('share');
        
    }
    /*
     * 获取jsapi_ticket
     * */
    public function getJsapiTicket(){
        //缓存存储
        if($_SESSION['jsapi_ticket_expire_time']>time()&&$_SESSION['jsapi_ticket']){
            $jsapi_ticket=$_SESSION['jsapi_ticket'];
            return $jsapi_ticket;
        }else{
            $token=new Index();
            $access_token=$token->getWxAccessToken();
            $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
            $jsapi=$token->http_curl($url);
            $jsapi_ticket=$jsapi['ticket'];
            
            $_SESSION['jsapi_ticket']=$jsapi_ticket;
            $_SESSION['jsapi_ticket_expire_time']=time()+7000;
            return $jsapi_ticket;
        }

    }
    /*
     * 获取16位随机码
     * */
    public function getRandCode(){
        $array=array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            '0','1','2','3','4','5','6','7','8','9'
        );
        $noncestr='';
        $max=count($array);
        for($i=1;$i<=16;$i++){
            $key=rand(1,$max);
            $noncestr.=$array[$key];
        }
        return $noncestr;
    }

}
