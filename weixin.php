<?php
/**
 * Created by PhpStorm.
 * User: HC
 * Date: 2016/12/9
 * Time: 10:33
 */
/*前提是：需要公网可以登录的域名，可成功访问，再将自己的文件上传即可
 * 1.将timestamp，nonce，token，按字典序排序；
 * 2.将排序后的三个参数拼接之后用sha1加密；
 * 3.将加密后的字符串与signature进行对比，判断该请求是否来自微信。
 *
 * */
$timestamp=$_GET['timestamp'];
$nonce=$_GET['nonce'];
$token='';                        //申请时填写的；
$signature=$_GET['signature'];
$array=array($timestamp,$nonce,$token);
sort($array);

$tmpstr=implode('',$array);       //拼接
$tmpstr=sha1($tmpstr);

if($tmpstr==$signature){
    echo $_GET['echostr'];
    exit;
}


//提交验证；在微信公众号中提交token验证。
