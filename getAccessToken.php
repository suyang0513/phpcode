<?php
/**
 * Created by PhpStorm.
 * User: SuYang
 * Date: 2017/3/29
 * Time: 11:48
 * 获取微信的access_token
 */
class getAccessToken
{
    private $appId='wx304bfdc6e5e971b1';                                              //APP Id
    private $appSecret='83a6800ce0cdce873f7f606e4acf3a74';             //APP 密匙
    public $accessToken;
    public function __construct()
    {
        $this->getFromLocal();                                                                         //先从本地找因为每天调用接口获取token的次数是固定的
    }

    public function getFromLocal()                                                              //从本地找
    {
        $result=file_get_contents('access_token.txt');                                    //access_token.txt用于保存token
        if(empty($result))
        {
            $this->getFromWechat();
        }
        else
        {
            $arr = explode(',', $result);
            if ((time() - (int)$arr[2]) < ((int)$arr[1] - 60))                                  //比微信规定的过期时间少一分钟目的是防止刚拿到token然后还没有执行后续动作token就已经过期
            {
                $this->accessToken = $arr[0];                                                     //返回结果
            } else {
                $this->getFromWechat();                                                            //如果超过了限制时间就重新申请
            }
        }
    }

    public function getFromWechat()                                                          //向微信申请token
    {
        $path="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret"; //请求地址
        $result=file_get_contents($path);
        $result=json_decode($result,true);
        $put=$result['access_token'].','.$result['expires_in'].','.time();
        file_put_contents('access_token.txt',$put);                                         //存到本地
        $this->accessToken=$result['access_token'];
    }

    public function returnToken()
    {
        return $this->accessToken;                                                                 //返回结果
    }

}