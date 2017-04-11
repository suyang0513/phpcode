<?php
/*$token='summerlovefootball';
$timestamp=$_GET['timestamp'];
$nonce=$_GET['nonce'];
$signature=$_GET['signature'];
$echostr=$_GET['echostr'];
$array=array();
$array=array($nonce,$timestamp,$token);
sort($array);
$str=implode($array);
$str=sha1($str);
if($str==$signature)
{
    header("Content-type:text");
    echo $echostr;
}
exit;*/
class response
{
    public $post;
    public $to;
    public $from;
    public $textTmp='<xml>
                                    <ToUserName><![CDATA[%s]]></ToUserName>
                                    <FromUserName><![CDATA[%s]]></FromUserName>
                                    <CreateTime>%s</CreateTime>
                                    <MsgType><![CDATA[text]]></MsgType>
                                    <Content><![CDATA[%s]]></Content>
                                    </xml>';
    public $imageTmp='<xml>
                                                 <ToUserName><![CDATA[%s]]></ToUserName>
                                                 <FromUserName><![CDATA[%s]]></FromUserName>
                                                 <CreateTime>%s</CreateTime>
                                                 <MsgType><![CDATA[news]]></MsgType>
                                                 <ArticleCount>2</ArticleCount>
                                                 <Articles>
                                                                 <item>
                                                                           <Title><![CDATA[%s]]></Title> 
                                                                           <Description><![CDATA[%s]]></Description>
                                                                           <PicUrl><![CDATA[%s]]></PicUrl>
                                                                           <Url><![CDATA[%s]]></Url>
                                                                 </item>
                                                                 <item>
                                                                           <Title><![CDATA[%s]]></Title>
                                                                           <Description><![CDATA[%s]]></Description>
                                                                           <PicUrl><![CDATA[%s]]></PicUrl>
                                                                           <Url><![CDATA[%s]]></Url>
                                                                 </item>
                                                 </Articles>
                                       </xml>';
    public $voiceTmp='<xml>
                                      <ToUserName><![CDATA[toUser]]></ToUserName>
                                      <FromUserName><![CDATA[fromUser]]></FromUserName>
                                      <CreateTime>1357290913</CreateTime>
                                      <MsgType><![CDATA[voice]]></MsgType>
                                      <MediaId><![CDATA[media_id]]></MediaId>
                                      <Format><![CDATA[Format]]></Format>
                                      <MsgId>1234567890123456</MsgId>
                                      </xml>';
    public $videoTmp='<xml>
                                       <ToUserName><![CDATA[toUser]]></ToUserName>
                                       <FromUserName><![CDATA[fromUser]]></FromUserName>
                                       <CreateTime>1357290913</CreateTime>
                                       <MsgType><![CDATA[video]]></MsgType>
                                       <MediaId><![CDATA[media_id]]></MediaId>
                                       <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
                                       <MsgId>1234567890123456</MsgId>
                                       </xml>';
    public $shortvideoTmp='<xml>
                                                <ToUserName><![CDATA[toUser]]></ToUserName>
                                                <FromUserName><![CDATA[fromUser]]></FromUserName>
                                                <CreateTime>1357290913</CreateTime>
                                                <MsgType><![CDATA[shortvideo]]></MsgType>
                                                <MediaId><![CDATA[media_id]]></MediaId>
                                                <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
                                                <MsgId>1234567890123456</MsgId>
                                                </xml>';
    public $location='<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[fromUser]]></FromUserName>
                                    <CreateTime>1351776360</CreateTime>
                                    <MsgType><![CDATA[location]]></MsgType>
                                    <Location_X>23.134521</Location_X>
                                    <Location_Y>113.358803</Location_Y>
                                    <Scale>20</Scale>
                                    <Label><![CDATA[位置信息]]></Label>
                                    <MsgId>1234567890123456</MsgId>
                                    </xml> ';
    public $linkTmp='<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[fromUser]]></FromUserName>
                                    <CreateTime>1351776360</CreateTime>
                                    <MsgType><![CDATA[link]]></MsgType>
                                    <Title><![CDATA[公众平台官网链接]]></Title>
                                    <Description><![CDATA[公众平台官网链接]]></Description>
                                    <Url><![CDATA[url]]></Url>
                                    <MsgId>1234567890123456</MsgId>
                                    </xml> ';
    public function __construct($postFiles)
    {
        $this->post=simplexml_load_string($postFiles);
        $this->to=$this->post->FromUserName;
        $this->from=$this->post->ToUserName;
        switch (strtolower($this->post->MsgType))
        {
            case 'text': $this->responseText($this->post->Content);break;
            case 'image': $this->responseImg();break;
            case 'voice': $this->responseVoice();break;
            case 'video': $this->responseVideo();break;
            case 'shortvideo': $this->responseShortvideo();break;
            case 'location':$this->responseLocation();break;
            case 'link':$this->responseLink();break;
            case 'event':$this->responseEvent();break;
            default: $this->responseText('暂时无法给您提供服务');
        }
    }

    public function responseText($content)
    {
        $template=$this->textTmp;
        echo sprintf($template,$this->to,$this->from,time(),$content);
    }

    public function responseLocation()
    {
    }

    public function responseImg()                                                               //回复图文消息的方法
    {
        $template=$this->imageTmp;
        $data=array(
            $this->to,
            $this->from,
            time(),
            'item1'=>array(
                '我们是天津权健的球迷',
                '我们热爱权健也热爱泰达',
                'https://timgsa.baidu.com/timg?image&quality=80&size=b10000_10000&sec=1491905786&di=f02b42c2de47475d1505c9b8aac64557&src=http://www.people.com.cn/mediafile/pic/20170407/94/2252247319622835922.jpg',
                'http://sports.sina.com.cn/china/j/2017-04-09/doc-ifyeceza1764419.shtml'
            ),
            'item2'=>array(
                '我们是天津泰达的球迷',
                '我们热爱泰达也热爱权健',
                'https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=1437168372,2707357244&fm=23&gp=0.jpg',
                'http://sports.sina.com.cn/china/j/2017-04-08/doc-ifyecezv2558145.shtml'
            ),
        );

        echo sprintf($template,$data[0],$data[1],$data[2],
            $data['item1'][0],$data['item1'][1],$data['item1'][2],$data['item1'][3],
            $data['item2'][0],$data['item2'][1],$data['item2'][2],$data['item2'][3]
        );

    }

    public function responseVoice()
    {}

    public function responseVideo()
    {}

    public function responseShortvideo()
    {}

    public function responseLink()
    {}

    public function responseEvent()
    {
        $evenType=strtolower($this->post->Event);
        switch ($evenType)
        {
            case 'location':
                file_put_contents("longitude.txt",$this->post->Longitude);
                file_put_contents("latitude.txt",$this->post->Latitude);
                break;
            case 'unsubscribe':break;
            case 'subscribe':$this->responseText("感谢您的关注!");break;
            case 'click':$this->responseClick();break;
        }

    }

    public function responseClick()
    {
        $eventType=$this->post->EventKey;
        switch($eventType)
        {
            case 'todayweather':
                if($reply=$this->getWeather()) $this->responseText($reply);
                else $this->responseText('微信无法获取您的位置信息');
                break;
            case 'aboutus':$this->responseImg();
        }
    }

    public function getWeather()
    {
        $longitude=file_get_contents('longitude.txt');
        $latitude=file_get_contents('latitude.txt');
        $path="http://api.map.baidu.com/telematics/v3/weather?location=$longitude,$latitude&output=json&ak=6tYzTvGZSOpYB5Oc2YGGOKt8";
        $result=file_get_contents($path);
        $result=json_decode($result,true);
        if($result['status']=='success')
        {
            $city=$result['results'][0]['currentCity'];
            $pm25=$result['results'][0]['pm25'];
            $item=$result['results'][0]['weather_data'][0];
            $str=$city."\n".'PM2.5:'.$pm25."\n".$item['date']."\n".$item['weather']."\n".$item['wind']."\n".$item['temperature'];
            return $str;
        }
        else return false;
    }
}
$postFiles=$GLOBALS['HTTP_RAW_POST_DATA'];
if($postFiles)
{
    file_put_contents('1.txt',$postFiles);
    $res=new response($postFiles);
}

