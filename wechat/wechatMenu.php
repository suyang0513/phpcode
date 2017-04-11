<?php
/**
 * Created by PhpStorm.
 * User: SuYang
 * Date: 2017/3/29
 * Time: 14:18
 * 自定义微信公众号菜单
 */
$menu='
{
     "button":
   [
     {       
          "name":"足球新闻",
          "sub_button":
        [
           {    
               "type":"view",
               "name":"欧冠",
               "url":"http://sports.sina.com.cn/g/championsleague/"
            },
            {
               "type":"view",
               "name":"中超",
               "url":"http://sports.sina.com.cn/csl/"
            },
            {
               "type":"view",
               "name":"亚冠",
               "url":"http://sports.sina.com.cn/z/AFCCL/"
            }
         ]
      },
      {
         "name":"今日之星",
         "sub_button":
         [
            {
               "type":"view",
               "name":"法比奥·卡纳瓦罗",
               "url":"http://baike.baidu.com/link?url=zDwvI56IREBHicP4ZsadLxcvIbZJSD1uUS8uud9HfE02Hd6iCxGmU8L_O8f1pvEQUy6vr12zO5hZJo123N3nfHigQe6x9dMX-xURze6U_JVhgxfRpdWavUy-zwRmP412KkOvQHYkDH69uQnVnpyXcMQ9w0VHeMf_3gY23chQc8Zi_gPEID388UGEstmzBhPDVsbM1MP6wsEN45suire_fkh3Emk62Ol7Otgl_4WESPfKGRM5Ko4wI5HWTJNikbtU"
            },
            {
               "type":"view",
               "name":"达尼埃莱·德罗西",
               "url":"http://baike.baidu.com/link?url=1lyq51D8NnE8szgQVRHDCYJG0Rmf3L8QLtljyAwWqrTfsHw_ya1jZRLUvTYGnAz3Ia8bauMsUjdz0qiJeMiGc-oi0f1Q1OEEeFENgB5OH0Qg0gm-Z9I8954pPZfiJbmnGwF3Lx_afUZg5LHIA47f46gFTqaDofQ-86mD9cOTxx_uoR_2dcCTqHgMIhS8skLfRkoC0RrC40ZZTRGAmAYpr35weLYSGYAMtD_2DAcPMMn15Z63uqn7QHkszkc0TuWp"
            },
            {
               "type":"view",
               "name":"米洛斯拉夫·克洛泽",
               "url":"http://baike.baidu.com/link?url=0FG6pu36yBEE-Hu6WXpcldoGVocOQeAXgaoE2JdkkC5nT4C4acVMfOZFW_JgC7yaIRNO2MECkm84paBhwLpz5GiNyfK9JWIySH9WMHviRPEty66lwbxN00apPUrcr0wriv4isv60n3aCVLjjqsrwLH40QFg0hF3qEh6sDHjfIjGGN-XctTuKiOk_YdS0-DznRfIexpbPbnuzJBKbuWQd80jbNMUGFlG2i6GKBpewAZMFIuTzfHBWtbI4jS82-CiT"
            }
         ]
      },
      {
         "name":"服务",
         "sub_button":
         [
           {
            "name": "我的位置", 
            "type": "location_select", 
            "key": "rselfmenu_2_0"
           },
           {
              "name": "关于我们", 
              "type": "click", 
              "key": "aboutus"
           },
           {
              "name": "今日天气", 
              "type": "click", 
              "key": "todayweather"
           }  
         ]
      }
   ]
 }'
;
function Curl($menu)
{
    include_once "getAccessToken.php";
    $obj=new getAccessToken();
    $token=$obj->returnToken();
    $url= "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    if(!empty($menu))
    {
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$menu);
    }
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    echo $result;
}
Curl($menu);