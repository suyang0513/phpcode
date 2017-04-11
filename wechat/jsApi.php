<?php
/**
 * Created by PhpStorm.
 * User: SuYang
 * Date: 2017/3/31
 * Time: 13:29
 * 微信页面授权--(JS-SDK使用权限签名算法)包括access_token的获取
 */
header("Content-Type:text/html;Charset=utf-8");
class jsApi{
    private $appId='wx3f854cc55f96cfc2';
    private $appSecret='150b482eab0a6daa6a1f6ff19446d271';
    private $accessTokenUrl;
    private $accessToken;
    private $jsApiTicket;
    private $signPackage;
    private $url;
    public function __construct()
    {
        $this->accessTokenUrl= 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId.'&secret='.$this->appSecret;
    }
    public function getAccessToken()
    {
        if(file_exists('access_token.txt'))                                         //判断有没有本地记录
        {
            $result=file_get_contents('access_token.txt');
            if(empty($result))
            {
                $this->getAccessTokenFromWeb();                                            //通过接口获取
            }
            else
            {
                $arr = explode(',', $result);
                if ((time() - (int)$arr[2]) < ((int)$arr[1] - 10))                              //比微信规定的过期时间少十秒钟目的是防止刚拿到token然后还没有执行后续动作token就已经过期
                {
                    $this->accessToken = $arr[0];
                }
                else
                {
                    $this->getAccessTokenFromWeb();
                }
            }
        }
        else
        {
            $fp=fopen('access_token.txt', "w+");                //先建一个新的空的文件
            fclose($fp);
            $this->getAccessTokenFromWeb();
        }
        $this->getAccessTokenFromWeb();
    }
    private function getAccessTokenFromWeb()
    {
        $time=time();
        $res = $this->api_request($this->accessTokenUrl);
        $this->accessToken=$res['access_token'];
        $put=$res['access_token'].','.$res['expires_in'].','.$time;
        file_put_contents('access_token.txt',$put);                       //写入本地文件
    }
    public function getJsApiTicket()
    {
        if(file_exists('ticket.txt'))
        {
            $result=file_get_contents('ticket.txt');
            if(empty($result))
            {
                $this->getJsApiTicketFromWeb();
            }
            else
            {
                $arr = explode(',', $result);
                if ((time() - (int)$arr[2]) < ((int)$arr[1] - 10))                              //比微信规定的过期时间少十秒钟目的是防止刚拿到token然后还没有执行后续动作token就已经过期
                {
                    $this->jsApiTicket= $arr[0];
                }
                else
                {
                    $this->getJsApiTicketFromWeb();
                }
            }
        }
        else
        {
            $fp=fopen('ticket.txt', "w+");                            //先建一个新的空的文件
            fclose($fp);
            $this->getJsApiTicketFromWeb();
        }
        //$this->getJsApiTicketFromWeb();
    }
    private function getJsApiTicketFromWeb()
    {
        $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->accessToken.'&type=jsapi';
        $time=time();
        $res = $this->api_request($url);
        $this->jsApiTicket=$res['ticket'];
        $put=$res['ticket'].','.$res['expires_in'].','.$time;
        file_put_contents('ticket.txt',$put);                                    //写入本地文件
    }
    public function getSignPackage()
    {
        $this->getJsApiTicket();
        $nonceStr = $this->getNonceStr();
        $timestamp = time();
        $url = $this->getUrl();
        $string = "jsapi_ticket=$this->jsApiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $this->signPackage=array('timestamp'=>$timestamp,'nonceStr'=>$nonceStr,'signature'=>$signature,
                                                       'appId'=>$this->appId,'url'=>$this->url,'ticket'=>$this->jsApiTicket,'token'=>$this->accessToken);
    }
    private function getNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $nonceStr = "";
        for ($i = 0; $i < $length; $i++) {
            $nonceStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $nonceStr;
    }
    private function getUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->url=$url;
        return $url;
    }
    private function api_request($url,$data=null){
        $ch = curl_init();                                                                                     //初始化cURL方法
        $opts = array(                                                                                       //设置cURL参数（基本参数）
            CURLOPT_SSL_VERIFYPEER => false,                                              //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            CURLOPT_SSL_VERIFYHOST => false,                                             //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_TIMEOUT => 500,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        );
        curl_setopt_array($ch, $opts);                                                              //post请求参数
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $output = curl_exec($ch);                                                                     //执行cURL操作
        if (curl_errno($ch)) {                                                                               //cURL操作发生错误处理。
            var_dump(curl_error($ch));
            die;
        }
        curl_close($ch);                                                                                      //关闭cURL
        $res = json_decode($output,true);
        return ($res);                                                                                         //返回数据,格式为数组
    }
    public function returnAccessToken()
    {
        return $this->accessToken;
    }
    public function returnSignPackage()
    {
        return $this->signPackage;
    }
    public function returnJsApiTicket()
    {
        return $this->jsApiTicket;
    }

}

$obj=new jsApi();
$obj->getSignPackage();
$signPackage=$obj->returnSignPackage();
$news = array("Title" =>"微信公众平台开发实践",
                        "Description"=>"本书共分10章，案例程序采用广泛流行的PHP、MySQL、XML、CSS、JavaScript、HTML5等程序语言及数据库实现。",
                        "PicUrl" =>'http://image.xinmin.cn/2013/04/08/20130408165329025106.jpg',
                         "Url" =>'www.baidu.com');
?>

<meta>
<head>
<meta charset="UTF-8">
</head>
<body>
<button id="show" type="button">开始</button>
</body>

</html>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    wx.config({
        url:'<?php echo $signPackage["url"];?>',
        ticket:'<?php echo $signPackage["ticket"];?>',
        token:'<?php echo $signPackage["token"];?>',
        debug: true,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi',
            'openLocation',
            'getLocation',
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ]
    });
    wx.ready(function () {
        wx.checkJsApi({                                                                                     //判断微信客户端是否支持相应的接口
         jsApiList: [
             'checkJsApi',
             'openLocation',
             'getLocation',
             'onMenuShareTimeline',
             'onMenuShareAppMessage'
         ],
         success: function (res) {
         if (res.checkResult.getLocation == false) {
         alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
         return;
         }
         }
         });
         wx.error(function(){alert('config失败')});
         wx.getLocation({
         success: function (res) {
         var latitude = res.latitude;                                                                   // 纬度，浮点数，范围为90 ~ -90
         var longitude = res.longitude;                                                            // 经度，浮点数，范围为180 ~ -180。
         var speed = res.speed;                                                                        // 速度，以米/每秒计
         var accuracy = res.accuracy;                                                               // 位置精度
         },
         cancel: function (res) {
         alert('用户拒绝授权获取地理位置');
         }
         });
        wx.error(function(res){                                                                         // config信息验证失败会执行error函数
            alert('failed');
            alert(location.href.split('#')[0]);
            console.log(res);
        });
        wx.onMenuShareAppMessage({
            title: '<?php echo $news['Title'];?>',
            desc: '<?php echo $news['Description'];?>',
            link: '<?php echo $news['Url'];?>',
            imgUrl: '<?php echo $news['PicUrl'];?>',
            trigger: function (res) {
                // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                // alert('用户点击发送给朋友');
            },
            success: function (res) {
                // alert('已分享');
            },
            cancel: function (res) {
                // alert('已取消');
            },
            fail: function (res) {
                // alert(JSON.stringify(res));
            }
        });
        wx.onMenuShareTimeline({
            title: '<?php echo $news['Title'];?>',
            link: '<?php echo $news['Url'];?>',
            imgUrl: '<?php echo $news['PicUrl'];?>',
            trigger: function (res) {
                // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                // alert('用户点击分享到朋友圈');
            },
            success: function (res) {
                // alert('已分享');
            },
            cancel: function (res) {
                // alert('已取消');
            },
            fail: function (res) {
                // alert(JSON.stringify(res));
            }
        });
    });

    document.getElementById('show').onclick=function () {

    }
</script>



