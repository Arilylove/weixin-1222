<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/20
 * Time: 16:55
 */
namespace app\index\controller;
use think\Controller;
use app\index\controller\Index;
class Oauth extends Controller{
    /*
     * 微信网页授权：
     *  1、引导用户进入授权页面同意授权，获取code
        2、通过code换取网页授权access_token（与基础支持中的access_token不同）
        3、如果需要，开发者可以刷新网页授权access_token，避免过期
        4、通过网页授权access_token和openid获取用户基本信息（支持UnionID机制）
     * */
    /*
     * 基本授权 snsapi_base;
     * */
    public function getBaseInfo(){             //地址（域名）可以生成二维码
        //1、引导用户进入授权页面同意授权，获取code
        $appid='';
        $redirect_uri=urlencode('..../getUserOpenId');//域名
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        header('Location:'.$url);

    }
    public function getUserOpenId(){
        //2、通过code换取网页授权access_token
        $appid='';
        $appsecret='';
        $code=$_GET['code'];             //上面获取
        $url=" https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
        $token=new Index();
        $res=$token->http_curl($url,'get');
        var_dump($res);
        $open_id=$res['openid'];
        //time();进行访问次数的设置
    }

    /*
     * 用户授权 snsapi_userinfo;
     * */
    public function getDetail(){
        $appid='';
        $redirect_uri=urlencode('..../getUserInfo');//域名
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header('Location:'.$url);
    }
    public function getUserInfo(){
        $appid='';
        $appsecret='';
        $code=$_GET['code'];             //上面获取
        $url=" https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
        $token=new Index();
        $res=$token->http_curl($url,'get');
        //var_dump($res);
        $accessToken=$res['access_token'];
        $open_id=$res['openid'];
        //3.通过网页授权access_token和openid获取用户基本信息
        $uurl="https://api.weixin.qq.com/sns/userinfo?access_token=".$accessToken."&openid=".$open_id."&lang=zh_CN";
        $result=$token->http_curl($uurl,'get');
        var_dump($result);
    }


}
