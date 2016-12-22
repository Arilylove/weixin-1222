<?php
namespace app\index\controller;
use app\index\controller\Weixin;
use think\Controller;
class Index extends Controller
{
    public function __construct()
    {
    }

    /* 开发者选项-》启用-》修改配置-》提交*/
    public function index()
    {
        //获得参数signature nonce token timestamp echostr
        $nonce=$_GET['nonce'];
        $token='';
        $timestamp=$_GET['timestamp'];
        $echostr=$_GET['echostr'];
        $signature=$_GET['signature'];
        //形成数组，然后按字典序排开
        $array=array();
        $array=array($nonce,$timestamp,$token);
        sort($array);
        //拼接成字符串，sha1加密，然后与signature进行校验
        $str=sha1(implode($array));
        if($str==$signature&&$echostr){
            //第一次接入weixinapi接口时验证
            echo $echostr;
            exit;
        }else{
            return $this->responseMsg();
        }
    }

    //接收事件推送并回复(点击关注的瞬间会产生的消息回复)
    public function responseMsg(){
        //1.获取到微信推送过来得post数据（xml格式）
        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];

        //2.处理消息类型，并设置回复类型和内容

        //将xml格式转化为对象
        $postobj=simplexml_load_string($postArr);
        //$postobj->ToUserName='';
        //$postobj->FromUserName='';
        //$postobj->CreateTime='';
        ////$postobj->MsgType='';
        //$postobj->Event='';
        //判断该数据是否是订阅的事件推送
        if(strtolower($postobj->MsgType)=='event'){
            //如果是关注subcribe事件
            if(strtolower($postobj->Event)=='subcribe'){
                //回复用户消息
                $toUser=$postobj->ToUserName;
                $fromUser=$postobj->FromUserName;
                $time=time();
                $msgType='text';
                $content='欢迎关注公众号'; //回复消息（可以测试显示$toUser，$fromUser。。。）
                $template="<xml>
                <ToUserName><![CDATA[toUser]]></ToUserName>
                <FromUserName><![CDATA[fromUser]]></FromUserName>
                <CreateTime>12345678</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[你好]]></Content>
                </xml>";
                $info=sprintf($template,$fromUser,$toUser,$time,$msgType,$content);
                echo $info;
            }
        }
    }

    /*
     * 消息回复（设置接收什么样的消息时显示什么样的回复）
     * */
    public function responseText(){

        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];

        $postobj=simplexml_load_string($postArr);
        if(strtolower($postobj->MsgType)=='text'){
          switch(trim($postobj->Content)){ //多个关键字（设置自动回复）
              case 1:
                  $content='1';
                  break;
              case 2:
                  $content='2';
                  break;
              case 3:
                  $content='3';
                  break;
              case 4:           //添加链接
                  $content='<a href="http://www.baidu.com/">百度</a>';
                  break;
              case '纯文本':
                  $content='纯文本';
                  break;
          }
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
            echo $info;

        }
    }
    /*
     * 回复图文
     * */
    public function responsePic(){
        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];
        $postobj=simplexml_load_string($postArr);
        if(strtolower($postobj->MsgType)=='图文'){
            switch(trim($postobj->Content)){ //多个关键字（设置自动回复）
                case 1:
                    $content='1';
                    break;
                case 2:
                    $content='2';
                    break;
                case 3:
                    $content='3';
                    break;
                case 4:           //添加链接
                    $content='<a href="http://www.baidu.com/">百度</a>';
                    break;
                case '图文':
                    $content='图文';
                    break;
            }
            //进行多图文消息回复时，子消息个数不能超过10个！！！！！！！！！！！
            $arr=array(          //不是必须；多图文则再添加几个数组内容
                array(
                   'title'=>'图文消息标题',
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

    /*
     * 综合回复
     * */
    public function response(){
        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];
        $postobj=simplexml_load_string($postArr);
        $weixin=new Weixin();
        if(strtolower($postobj->MsgType)=='event'){
            if(strtolower($postobj->Event)=='subcribe'){
                //关注回复
                $content='欢迎关注公众号';
                $res=$weixin->responseSubscribe($postobj,$content);
                return $res;
            }
        }else if(strtolower($postobj->MsgType)=='text') {
            switch(trim($postobj->Content)){
                case 1:
                    $content='1';
                    break;
                case 2:
                    $content='2';
                    break;
                case 3:
                    $content='<a href="http://www.baidu.com">百度</a>';
                    break;
                default:
                    break;
            }
            $text=$weixin->responseText($postobj,$content);
            return $text;
        }else if(strtolower($postobj->MsgType)=='图文'){
            $arr=array(          //不是必须；多图文则再添加几个数组内容
                array(
                    'title'=>'图文消息标题',
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
            $content='图文内容';
            $pic=$weixin->responsePic($postobj,$arr,$content);
            return $pic;
        }

    }
    /*
     * curl例子
     * */
  /*  function http_curl(){
        //获取imooc
        //1.初始化curl
        $ch = curl_init();
        $url = 'http://www.baidu.com';
        //2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        var_dump($output);
    }*/

    /*
     * $url 接口url string
     * $type 请求类型 string
     * $res 返回数据类型 string
     * $arr post请求参数 string
     * */
    function http_curl($url,$type='get',$res='json',$arr=''){
        //获取imooc
        //1.初始化curl
        $ch = curl_init();
        //$url = 'http://www.baidu.com';
        //2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($type=='post'){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,$arr);
        }
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        if($res=='json'){
            if(curl_errno($ch)){
                return curl_error($ch);
            }
            return json_decode($output,true);
        }
        //return $output;
    }

    /*
     * 获取access_token
     * */
    public function getWxAccessToken(){
        //1.请求url地址
        $appid='';
        $appsecret='';
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        //2.初始化
        $ch=curl_init();
        //3.设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //4.调用接口
        $res=curl_exec($ch);
        //5.关闭curl
        curl_close($ch);
        //打印看下是否有错误，什么错误
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        $output=json_decode($res,true);
        var_dump($output);
    }
    /*
     * 获取微信服务器ip
     * */
    public function getServerIp(){
        $accessToken='';//上面获取到的
        $url='https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accessToken;
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res=curl_exec($ch);
        curl_close($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        $outputIP=json_decode($res,true);
        var_dump($outputIP);              //获取IP
    }

    /*
     * 天气
     * */
    public function weather(){
        $ch = curl_init();
        $url = 'http://apis.baidu.com/tianyiweather/basicforecast/weatherapi?areaid=101010100';
        $header = array(
            'apikey: 您自己的apikey',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);

        var_dump(json_decode($res));
    }
    /*
     * 天气示例
     * {
    "air": {//空气质量
        "101010100": {//站点
            "2001006": {//空气质量编号
                "006": "2",//SO2
                "007": "5",//CO
                "003": "17",//NO2
                "004": "16",//O3
                "000": "201603250900",//更新时间
                "001": "16",//PM2.5
                "005": "31",//PM10
                "002": "31"//AQI
            }
        }
    },
    "alarm": {//预警
        "101010100": {//城市站号
            "1001003": {//预警编号
            "001": "北京市",//预警发布单位省级名称
            "002": "北京市",//预警发布单位市级名称
            "003": "北京市",//预警发布单位县级名称
            "004": 13,//预警类别编号
            "005": "霾",//预警类别名称
            "006": "02",//预警级别编号
            "007": "黄色",//预警级别名称
            "008": "2016-01-14 11:34",//预警发布时间
            "009": "霸州气象台1月14日11时发布霾黄色预警信号：预计今天中午到夜间，受静稳形势控制，全市有中度霾，局地重度霾，气象条件不利于空气污染物扩散，建议市民朋友做好防范工作，老人、儿童等易感人群减少户外活动。",//预警发布内容
            "010": "201601141133545183霾黄色",//预警信息
            "011": "101010100-20160114113400-1302.html"//天气网跳转地址
            }
        }
    },
    "forecast": {//逐24小时预报
        "24h": {
            "101010100": {//站号
                "1001001": [天气预报编号
                    {
                        "003": "15",//白天温度
                        "004": "3",//晚上温度
                        "001": "00",//白天天气现象编码
                        "002": "00",//晚上天气现象编码
                        "006": "0",//晚上风力
                        "008": "0",//晚上方向
                        "005": "1",//白天风力
                        "007": "4"//白天方向
                    },//第一天
                    {
                        "003": "18",//白天温度
                        "004": "4",//晚上温度
                        "001": "00",//白天天气现象编码
                        "002": "00"//晚上天气现象编码
                        "006": "0",//晚上风力
                        "008": "0",//晚上方向
                        "005": "1",//白天风力
                        "007": "4"//白天方向
                    },//第二天
                    {
                        "003": "20",//白天温度
                        "004": "6",//晚上温度
                        "001": "00",//白天天气现象编码
                        "002": "01"//晚上天气现象编码
                        "006": "0",//晚上风力
                        "008": "0",//晚上方向
                        "005": "1",//白天风力
                        "007": "4"//白天方向
                    }//第三天
                ],
                "000": "201603250800"//预报更新时间
            }
        }
    },
    "observe": {//实况
        "101010100": {//站号
            "1001002": {//实况编号
                "006": "0",//当前降水量(单位是毫米)
                "007": "1028",//当前气压(单位百帕)
                "003": "3",//当前风力(单位是级,不用转码)
                "004": "8",//当前风向编号
                "000": "09:05",//实况发布时间
                "001": "01",//当前天气现象编号
                "005": "21",//当前湿度(单位%)
                "002": "9"//当前温度(单位摄氏度)
            }
        }
    },
    "index": {//指数
        "24h": {//逐24小时指数预报
            "101010100": {//站号
                "1001004": [//指数编号
                    {
                        "000": "20160325",//指数发布时间
                        "005": {
                            "005001": "交通指数",//指数名称
                            "005003": "天气较好，路面干燥，交通气象条件良好，车辆可以正常行驶。",//指数释义
                            "005002": "良好"//指数等级
                        },
                        "004": {
                            "004001": "感冒指数",
                            "004003": "昼夜温差较大，较易发生感冒，请适当增减衣服。体质较弱的朋友请注意防护。",
                            "004002": "较易发"
                        },
                        "002": {
                            "002002": "较冷",
                            "002003": "建议着厚外套加毛衣等服装。年老体弱者宜着大衣、呢外套加羊毛衫。",
                            "002001": "穿衣指数"
                        }
                    },
                    {
                        "000": "20160326",
                        "005": {
                            "005001": "交通指数",
                            "005003": "天气较好，路面干燥，交通气象条件良好，车辆可以正常行驶。",
                            "005002": "良好"
                        },
                        "004": {
                            "004001": "感冒指数",
                            "004003": "昼夜温差很大，易发生感冒，请注意适当增减衣服，加强自我防护避免感冒。",
                            "004002": "易发"
                        },
                        "002": {
                            "002002": "较舒适",
                            "002003": "建议着薄外套、开衫牛仔衫裤等服装。年老体弱者应适当添加衣物，宜着夹克衫、薄毛衣等。",
                            "002001": "穿衣指数"
                        }
                    },
                    {
                        "000": "20160327",
                        "005": {
                            "005001": "交通指数",
                            "005003": "天气较好，路面干燥，交通气象条件良好，车辆可以正常行驶。",
                            "005002": "良好"
                        },
                        "004": {
                            "004001": "感冒指数",
                            "004003": "昼夜温差较大，较易发生感冒，请适当增减衣服。体质较弱的朋友请注意防护。",
                            "004002": "较易发"
                        },
                        "002": {
                            "002002": "较舒适",
                            "002003": "建议着薄外套、开衫牛仔衫裤等服装。年老体弱者应适当添加衣物，宜着夹克衫、薄毛衣等。",
                            "002001": "穿衣指数"
                        }
                    }
                ],
                "000": "201603250800"//指数发布时间
            }
        }
    }
}
     * */


}
