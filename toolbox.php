<?php

class docOperation
{
    public $count_files;                                                                                   //统计文件数组
    public $count_folders;                                                                              //统计文件夹数组
    public $folder = array();                                                                           //文件夹数组
    public $file = array();                                                                                //文件数组
    public $arry_handle = array();                                                                 //文件夹句柄数组，没有他将删除不了根文件夹
    public $initialPath;
    public $destinyPath;

    public function Ergodic($path)
    {
        if(! file_exists($path)) die('The invalid file or folder path');
        if(is_dir($path))
        {
            $this -> is_folder($path);
            $this ->count_folders++;
        }
        else
        {
            $this ->is_files($path);
            $this -> count_files++;
        }
    }

    public function beginErgodic($path)
    {
        $this -> Ergodic($path);
        $this -> ergodicResults();
        $this -> clearArray();
    }

    public function is_folder($path)
    {
        array_push($this->folder,"$path");
        $handle = opendir($path);
        array_push($this ->arry_handle,$handle);
        while(($dirname = readdir($handle)) != null)                                   //readdir挨个读取文件夹里的文件
        {
            if ($dirname != '.' && $dirname != '..')
            {
                $newpath = "$path/$dirname";                                                  //子目录
                if (is_dir($newpath))                                                                     //is_dir的参数是一个路径
                {
                    $this->count_folders++;
                    $this->is_folder($newpath);                                                     //递归
                }
                else
                {
                    $this->count_files++;
                    array_push($this->file, "$newpath");

                }
            }

        }

    }

    public function is_files($path)
    {
        echo "File:"."&nbsp;&nbsp;$path &nbsp;&nbsp;is a single file.<br>";
        array_push($this ->file,"$path");
    }

    public function ergodicResults()
    {
        echo "The quantity of folders are   $this->count_folders"."<br><br>";
        echo "The quantity of files are   $this->count_files"."<br><br>";
        echo "****************The following are folders****************<br><br>";
        foreach ($this ->folder as $key => $value)
        {
            echo "The paths of folders are:"."&nbsp;&nbsp;$value<br>";
        }
        echo "<br><br>";
        echo "****************The following are files******************<br><br>";
        foreach ($this ->file as $key => $value)
        {
            echo "The paths of files are:"."&nbsp;&nbsp;$value<br>";
        }
    }

    public  function clearArray()
    {
        array_splice($this->file,0,count($this ->file));
        array_splice($this ->folder,0,count($this ->folder));
        array_splice($this ->arry_handle,0,count($this ->arry_handle));
        $this ->count_files = 0;
        $this -> count_folders = 0;
    }

    public function beginDelete($path)
    {
        $this ->Ergodic($path);
        $this -> deleteFilesAndFolders();
        $this -> deleteResults();
        $this -> clearArray();
    }

    public  function deleteFilesAndFolders()
    {
        for($i=0;$i<($this->count_files);$i++)
        {
            unlink($this ->file[$i]);                                                                      //先删文件
        }
        for($j=($this ->count_folders-1);$j>=0;$j--)
        {
            closedir($this ->arry_handle[$j]);                                                    //先关闭句柄
            rmdir($this ->folder[$j]);                                                                  //文件夹要从里到外删
        }
    }

    public  function  deleteResults()
    {
        echo "Totally delete file: $this->count_files<br>";
        echo "Totally delete folder: $this->count_folders<br>";
    }

    public  function judgement($path1,$path2)
    {
        if (!file_exists($path1)) die("the copied file don't exit");
        if (is_file($path1) && is_dir($path2)) die("A file can't cover a folder");
        if (is_dir($path1) && file_exists($path2)) die("The files has existed in target path");
    }

    public  function beginCopy($path1,$path2)
    {
        $this -> initialPath = $path1;
        $this -> destinyPath = $path2;
        $this ->judgement($path1,$path2);
        $this ->copyFiles($path1,$path2);
        $this ->copyResults();
    }

    public function copyFiles($path1,$path2)
    {
        if (is_file($path1)) copy($path1,$path2);                                            //如果是文件就直接复制
        else                                                                                                         //如果是文件夹还要打开文件夹
        {
            mkdir($path2);
            $handle = opendir($path1);
            while(($dirname = readdir($handle)) != null)
            {
                if ($dirname != '.' && $dirname != '..')
                {
                    $this ->copyFiles("$path1/$dirname","$path2/$dirname");

                }
            }
        }
    }

    public  function copyResults()
    {
        $this ->Ergodic($this -> destinyPath) ;
        echo"Totally copy ".$this ->count_folders." folders and ".$this ->count_files." files from&nbsp;".$this ->initialPath."&nbsp;to&nbsp;".$this ->destinyPath."<br>";
        $this ->clearArray();
    }

    public  function  beginCut($path1,$path2)
    {
        $this -> initialPath = $path1;
        $this -> destinyPath = $path2;
        $this ->judgement($path1,$path2);
        $this ->copyFiles($path1,$path2);
        $this ->Ergodic($path1);
        $this ->deleteFilesAndFolders();
        $this ->clearArray();
        $this ->cutResults();
    }

    public function cutResults()
    {
        $this ->Ergodic($this -> destinyPath) ;
        echo"Totally cut ".$this ->count_folders." folders and ".$this ->count_files." files from ".$this ->initialPath."&nbsp;to&nbsp;".$this ->destinyPath."<br>";
        $this ->clearArray();
    }

}                                                                                  //文件操作

class identifyingCode                                                                                   //生成验证码
{
    public $image;                                                                                             //框的宽度，由用户输入
    public $width;
    public $height;                                                                                             //框的长度，由用户输入
    public function __construct($width,$height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function randNum($max)                                                                  //生成随机数
    {
        return rand(0,$max);
    }

    public function randColor()
    {
        $color = imagecolorallocate($this->image,$this->randNum(255),$this->randNum(255),$this->randNum(255));//在this->image区域生成颜色
        return $color;
    }                                                                                                                  //生成随机颜色（点，线，背景，文字）

    public function productionPoint()
    {
        $pointNumber = $this->randNum($this->width+$this->height);              //点的最大数取决于验证框的大小
        for($i=0;$i<$pointNumber;$i++)
            imagesetpixel($this->image,$this->randNum($this->width),$this->randNum($this->height),$this->randColor());
    }

    public function productionLine()
    {
        $m=rand(0,5);                                                                                           //最多五条线
        for($i=0;$i<$m;$i++)
            imageline($this->image,$this->randNum($this->width),$this->randNum($this->height),
                $this->randNum($this->width),$this->randNum($this->height),$this->randColor());
    }

    public function productText()
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; //字符集
        $str1='';
        for ($i = 0; $i < 6; $i++) {                                                                          //默认生成六个字符因此循环六次
            $m = rand(0, strlen($str));
            $str1 .= substr($str, $m, 1);
        }
        imagettftext($this->image,(0.2*$this->width),rand(-5,5),(0.1*$this->width),(0.75*$this->height),$this->randColor(),'res/simsun.ttc',$str1);
    }

    public function identifyCode()
    {
        $this->image = imagecreatetruecolor($this->width,$this->height);
        imagefill($this->image, 0,0,$this->randColor());
        $this->productionPoint();
        $this->productText();
        $this-> productionLine();
        imagejpeg($this->image);
    }                                                                                                                   //生成验证码

}

class time                                                                                                       //获取时间返回的全是时间戳
{
    public $d;
    public $m;
    public $y;
    public $w;
    public $zeroToday;

    public function __construct()
    {
        $this->y = date('Y',time());
        $this->m = date('m',time());
        $this->d = date('d',time());
        $this->w = date('w',time());
        $this->zeroToday = mktime(0,0,0,$this->m,$this->d,$this->y);    //返回今天零点零时零分的时间戳
    }

    public function getZeroToday()                                                             //今天零点的时间戳
    {
        return $this->zeroToday;
    }

    public function getZeroWeek()                                                              //本周日零点的时间戳
    {
        return $this->zeroToday-($this->w)*3600*24;
    }

    public function getZeroLastWeek()                                                       //上周日零点的时间戳
    {
        return $this->getZeroWeek()-7*3600*24;
    }

    public function getZeroMonth()                                                            //本月一号零点的时间戳
    {
        return mktime(0,0,0,$this->m,1,$this->y);
    }

    public function getZeroLastMonth()                                                     //上月一号零点的时间戳
    {
        return mktime(0,0,0,($this->m)-1,1,$this->y);
    }

    public function getZeroYear()                                                                //今年一月一号零点的时间戳
    {
        return mktime(0,0,0,1,1,$this->y);
    }

    public function getZeroLastYear()                                                         //去年一月一号零点的时间戳
    {
        return mktime(0,0,0,1,1,($this->y)-1);
    }
}

class formIdentify
{
    public static function email($email)                                                      //判断邮箱
    {
        $pattern = '/^[\w\-\.]+@[\w\-]+[\.\w+]+$/';
         if(preg_match($pattern,$email)) return true;
         else return false;
    }

    public static function userName($userName)                                     //判断用户名
    {
        $pattern = '/^(?!_)(?!.*?_$)[\x{4e00}-\x{9fa5}\w]{1,80}$/u';              //用户名可以为字母数字汉字下划线但是不能以下划线开始和结束
        if(preg_match($pattern,$userName)) return true;
        else return false;
    }

    public static function password($password)                                        //判断密码。6-16未数字字母下划线但是不能以下划线开始结尾
    {
        $pattern='/^(?!_)(?!.*?_$)\w{6,16}$/';
        if(preg_match($pattern,$password)) return true;
        else return false;
    }
}                                                                                     //表单验证

class dbServer
{
    private $dbconfig=array(
        'type'=>'mysql',
        'host'=>'localhost',
        'port'=>'3306',
        'user'=>'',
        'pwd'=>'',
        'charset'=>'utf8',
        'dbname'=>'',
    );                                                                    //默认配置
    private $link;
    private static $instance;
    private $data=array();                                                                              //需要操作的数据

    private function __construct($params=array())
    {
        $this->initAttr($params);
        $this->connectServer();
        $this->setCharset();
        $this->selectDefaultDb();
    }

    private function initAttr($params)                                                         //params是自定义的配置，initAttr方法的作用是将自定义的配置与默认配置合并形成配置文件
    {
        $this->dbconfig=array_merge($this->dbconfig,$params);
    }

    private function connectServer()                                                            //连接数据库
    {
        $type=$this->dbconfig['type'];
        $host=$this->dbconfig['host'];
        $port=$this->dbconfig['port'];
        $user=$this->dbconfig['user'];
        $pwd=$this->dbconfig['pwd'];
        $charset=$this->dbconfig['charset'];
        $dsn="$type:host=$host;port=$port;charset=$charset";
        if($link=new PDO($dsn,$user,$pwd))
        {
            $this->link=$link;
        }
        else
        {
            die('数据库连接失败,请与管理员联系');
        }
    }

    private function setCharset()                                                                  //设置编码不用单独调用因为已在构造函数中调用
    {
        $sql="set names {$this->dbconfig['charset']}";
        $this->query($sql);
    }

    private function selectDefaultDb()                                                        //设置数据库不用单独调用因为已在构造函数中调用
    {
        if($this->dbconfig['dbname']=='') return;
        $sql="use `{$this->dbconfig['dbname']}`";
        $this->query($sql);
    }

    public function insertQuery($sql,$batch=false)                                  //PDO预处理函数
    {
        $data= $batch? $this->data : array($this->data);
        $this->data=array();
        $stmt=$this->link->prepare($sql);
        foreach ($data as $v)
        {
            if($stmt->execute($v)===false)
            {
                die('数据库操作失败，请与管理员联系');
            }
        }
        return $stmt;
    }

    public function createData($data)                                                         //将预处理需要的数据导入到对象中
    {
        $this->data=$data;
        return $this;
    }

    private function __clone(){}

    public function fetchRow($sql)                                                              //取一条记录
    {
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql)                                                                 //取所有记录
    {
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function query($sql)
    {
        return $this->link->query($sql);
    }

    public static function getInstance($params=array())                          //实例化函数
    {
        if(!self::$instance instanceof self)
        {
            self::$instance=new self($params);
        }
        return self::$instance;
    }
}                                                                                           //数据库通用类

class login
{
    protected $userId;
    protected  $pwd;
    public $res;
    public $dbConnect;
    public $format="/^\w{6,12}$/";
    public $sql;
    public $config=array('user'=>'root','pwd'=>'root','dbname'=>'forum');
    protected $data;
    public function __construct()
    {
        if(isset($_POST['userId']) && isset($_POST['userPassword']))
        {
            $this->userId=$_POST['userId'];
            $this->pwd=$_POST['userPassword'];
            if($this->checkOut($this->userId) && $this->checkOut($this->pwd))
            {
                $this->dbConnect=dbServer::getInstance($this->config);
                $this->sql="select pwd,name from member where id=$this->userId";
                $this->queryCheck($this->sql);
                $this->ajaxReturn();
            }
            else
            {
                $this->ajaxReturn();
            }
        }
        else
        {
            $this->res=array('status'=>0,'info'=>'用户名和密码不能为空');
            $this->ajaxReturn();
        }
    }

    private function queryCheck($sql)
    {
        $row=$this->dbConnect->fetchRow($sql);
        if($row[0]['pwd']==md5($this->pwd))
        {
            $_SESSION['user_id']=$row[0]['id'];
            $_SESSION['user_name']=$row[0]['name'];
            $this->res=array('info'=>"欢迎".$row[0]['name'],'status'=>1);
        }
        else
        {
            $this->res=array('info'=>'用户名或密码错误','status'=>0);
        }
    }

    public function checkOut($source)
    {
        if(preg_match($this->format,$source)) return true;
        else
        {
            $this->res=array('info'=>'用户名或密码格式错误','status'=>0);
            return false;
        }
    }

    protected function ajaxReturn()
    {
        echo json_encode($this->res);
    }

}                                                                                                 //登录

class register extends login
{
    public $userName;
    public $data;
    public function __construct()
    {
        if(isset($_POST['userName']) && isset($_POST['confirmPassword']) && isset($_POST['userId']) && isset($_POST['userPassword']) &&
            $_POST['userPassword']==$_POST['confirmPassword'])
        {
            $this->pwd=$_POST['userPassword'];
            $this->userId=$_POST['userId'];
            $this->userName=$_POST['userName'];
            if($this->checkOut($this->userId) && $this->checkOut($this->pwd))
            {
                $this->dbConnect=dbServer::getInstance($this->config);
                $this->sql="insert into member(id,`name`,pwd) values($this->userId,'$this->userName',$this->pwd)";
                if($this->dbConnect->execute($this->sql))
                {
                    $this->res=array('status'=>1,'info'=>'注册成功');
                    $this->ajaxReturn();
                }
                else
                {
                    $this->res=array('status'=>0,'info'=>'注册失败');
                    $this->ajaxReturn();
                }
            }
            else
            {
                $this->res=array('status'=>0,'info'=>'用户名或密码格式不对');
                $this->ajaxReturn();
            }
        }
        else
        {
            $this->res=array('status'=>'0','info'=>'信息输入有误');
            $this->ajaxReturn();
        }

    }
}                                                                   //注册

class exportExcel
{
    private $data;                                                                                            //需要输出的数组
    private $count_row;                                                                                  //有多少条记录
    private $count_field;                                                                                 //有多少个字段
    private $keys;                                                                                            //存放字段的数组
    private $phpExcel;                                                                                    //phpexcel对象
    private $activeSheet;                                                                                //当前活动的表
    private $fileName;                                                                                    //将导出的excel文件的名字

    public function __construct($data,$path,$title,$fileName)                 //data是数据，path是PHPExcel.php存在的文件夹（路径）,title是sheet名并不是文件名
    {                                                                                                                   //filename是文件名
        include_once $path."PHPExcel.php";
        $this->data=$data;
        $this->count_row=count($data);
        $this->count_field=count($data[0]);                                                  //每条记录拥有的字段的个数相等
        $this->fileName=$fileName;
        $this->keys=array_keys($data[0]);
        $this->phpExcel=new  PHPExcel();
        $this->phpExcel->setActiveSheetIndex(0);
        $this->activeSheet=$this->phpExcel->getActiveSheet();
        $this->activeSheet->setTitle($title);
        $this->createFields();
    }

    public function createFields()                                                                 //创建excel中的字段行
    {
        for($i=0;$i<$this->count_field;$i++)
        {
            $field = PHPExcel_Cell::stringFromColumnIndex($i);
            $position=$field. '1';
            $this->phpExcel->getActiveSheet()->setCellValue($position, $this->keys[$i]);
            $this->phpExcel->getActiveSheet()->getStyle($position)->getFont()->setBold(true); //加黑
            $this->phpExcel->getActiveSheet()->getColumnDimension($field)->setWidth(20);    //设置宽度
        }
        $this->createRows();
    }

    public function createRows()                                                                  //创建数据行
    {
        $k=1;
        foreach ($this->data as $key=>$value)                                            //外层循环写入某一行数据
        {
            $k++;
            for($i=0;$i<$this->count_field;$i++)                                             //内层循环写入某一行的某一个单元格的数据
            {
                $field=PHPExcel_Cell::stringFromColumnIndex($i).$k;
                $v=$value[$this->keys[$i]];
                $this->phpExcel->getActiveSheet()->setCellValue($field,$v);
            }
        }
        $this->outPut();
    }

    public function outPut()                                                                          //输出
    {
        header("Content-Type:application/force-download");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Type:application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.$this->fileName.'.xls"');
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}                                                                                      //将数组导出到excel

class myCrawl
{
    public $link;                                                                                               //PDO实例
    public $name;                                                                                            //商品名
    public $size;                                                                                               //尺寸
    public $color;                                                                                             //颜色
    public $price;                                                                                             //价钱
    public $discribe;                                                                                        //描述
    public $factoryId;                                                                                      //用户传过来的商品号
    public $sourceId;                                                                                       //从网页上抓取的商品号
    public $iniFactoryId;                                                                                 //原始格式的商品号，每三个数字一个点
    public $base='http://www.ikea.com/cn/zh/catalog/products/';         //存储商品图片的根目录
    public $urlBase='http://www.ikea.com';                                                 //商品信息的根目录
    public $path;                                                                                             //商品的路径
    public $result;                                                                                            //返回给前端的结果
    public $resource;                                                                                      //phpquery抓取来的资源
    public $picture;                                                                                         //存放商品图片的url的地址，用于返回给前端
    public $shortPicture=array();                                                                  //存放商品图片的url的地址，用于存入数据库
    public function __construct($config,$factory_id)
    {
        if($factory_id)
        {
            $this->factoryId=htmlspecialchars($factory_id);
            $this->factoryId=str_replace("　","",$this->factoryId);                //替换全角空格
            $this->factoryId=trim($this->factoryId);                                       //去掉两边的空格
            $this->iniFactoryId=$this->factoryId;                                            //用iniFactoryId保存一下原始的商品号因为下一步factoryId会变成没有点的形式
            $this->factoryId=(string)$this->getFactoryId($this->factoryId);//对用户传过来的商品号进行四布处理
            $this->link=dbServer::getInstance($config);                                  //创建数据库实例，先去数据库里面找
            $this->beginFind();
        }
        else
        {
            $this->result=array('info'=>'请输入宜家的产品货号','status'=>0);
            $this->returnData();
        }
    }
    public function getFactoryId($id)
    {
        $factoryId=$this->idFormat($id);                                                       //转换格式
        $factoryId=$this->identifyId($factoryId);                                          //确定要不要加S
        return $factoryId;
    }
    public function idFormat($id)
    {
        $array=explode('.',$id);
        $retStr=implode('',$array);
        return $retStr;
    }
    public function identifyId($id)
    {

        $url=$this->base.'S'.$id.'/';
        $array=get_headers($url);
        $pos=strrpos($array[0],'Forbidden');
        if($pos)
        {
            return $id;
        }
        else
        {
            return 'S'.$id;
        }
    }
    public function beginFind()
    {
        $sql="select factory_id,name,size,color,price,discribe from goods where factory_id='$this->iniFactoryId'";
        $urlSql="select url from goods_pic where factory_id='$this->iniFactoryId'";
        $result=$this->link->fetchRow($sql);                                                //查询有无此商品语句
        $urlResult=$this->link->fetchRow($urlSql)['url'];                             //查询有无url记录
        $urlData=explode(',',$urlResult);                                                         //将商品的突变的url变成数组保存
        foreach ($urlData as $k=>$v)                                                             //为每一个url加上固定的域名
        {
            $urlData[$k]=$this->urlBase.$v;
        }
        if($result)                                                                                               //如果有结果就不用去宜家抓取了
        {
            $this->result=$result;
            $this->result['url']=$urlData;
            $this->formatData();                                                                         //对抓取来的数据进行一些必要的处理
            $this->ajaxReturn();
        }
        else                                                                                                          //在本地找不到就去宜家抓取数据
        {
            $this->toIkea();
        }
    }
    public function toIkea()
    {
        $this->path=$this->base.$this->factoryId.'/';                                  //生成商品地址
        $this->resource=phpQuery::newDocumentFile($this->path);        //实例化phpquery类
        $this->name=$this->getName();
        $this->size=$this->getSize();
        $this->color=$this->getColor();
        $this->price=$this->getPrice();
        $this->discribe=$this->getDes();                                                       //获得描述
        $this->sourceId=$this->getSourceId();                                              //获得抓取页面中的商品id
        $this->picture=$this->getPicture();                                                   //获取商品的图片的url
        $this->formatData();                                                                             //处理一下获得的数据
        $this->insertItems();                                                                             //向数据库添加数据
    }
    public function getName()
    {
        $name=pq('#name')->html();
        $title=pq('#type')->html();
        return $name.$title;
    }
    public function getSize()
    {
        $obj=pq('.displayMeasurements')->html();
        if(!$obj) $obj=pq('.dropdown')->find('option:selected')->html();
        $position=mb_strpos($obj,'厘',0,'utf-8');
        $size=mb_substr($obj,0,$position-1,'utf-8');
        if($size)return $size.'厘米';
        else return'无';
    }
    public function insertItems()
    {
        $urlStr=implode(',',$this->shortPicture);                                            //将图片的短地址拼接成字符串便于存入数据库
        $sql="insert into goods(factory_id,name,size,color,price,channel_id,discribe) values('$this->sourceId','$this->name','$this->size','$this->color',$this->price,1,'$this->discribe')";
        $urlSql="insert into goods_pic(factory_id,url) values('$this->sourceId','$urlStr')";
        $result=$this->link->execute($sql);
        $resultUrl=$this->link->execute($urlSql);
        if($result && $resultUrl) $this->returnData();                                   //当且仅当两个sql都成功时才会给用户放回数据
        else
        {
            $this->result = array('info' => '查询失败', 'status' => '0');
            $this->ajaxReturn();
        }
    }
    public function getPicture()
    {
        $start=strrpos($this->resource,'"large":["');
        $str=substr($this->resource,$start+9);
        $end=strpos($str,'"]}');
        $return=substr($str,0,$end);
        $array=explode(',',$return);
        foreach ($array as $key=>$value)
        {
            $array[$key]=$this->urlBase.trim($value,'"');
            array_push($this->shortPicture,trim($value,'"'));
        }
        return $array;
    }
    public function getColor()
    {
        $str=pq('#type')->html();
        $position=mb_strpos($str,'色',0,'utf-8');
        return mb_substr($str,$position-2,3,'utf-8');
    }
    public function getPrice()
    {
        $price=pq('#price1')->html();
        if($price)
        {
            $price=$this->string_replace($price);                                            //先去掉英文的空格和换行
            for($i=0;$i<mb_strlen($price,'utf-8');$i++)                                   //这个循环是找到第一个数字
            {
                if((int)mb_substr($price,$i,1,'utf-8')!=0) break;
            }
            $newStr=mb_substr($price,$i,mb_strlen($price),'utf-8');             //从上一步的位置截取字符串
            $newStr=str_replace("，",",",$newStr);
            $newArray=explode(',',$newStr);
            $returnStr='';
            foreach ($newArray as $k=>$v)
            {
                $returnStr.=$v;
            }
            return (float)$returnStr;                                                                       //强制转换成浮点
        }
    }
    public function getDes()
    {
        $des=pq('#custMaterials')->html();
        $size=pq('#metric')->html();
        return $des.'安装后尺寸:'.$size;
    }
    public function getSourceId()
    {
        return (string)pq('#schemaProductId')->html();
    }
    public function string_replace($str)
    {
        if(isset($str) && is_string($str))
        {
            $newStr=preg_replace('/\r|\n|\t/', '', $str);                                     //替换换行和空格
            $newStr=strip_tags($newStr);                                                         //过滤html标签
            return $newStr;
        }
        else return '';

    }
    public function formatData()
    {
        $this->name=$this->string_replace($this->name);
        $this->size=$this->string_replace($this->size);
        $this->color=$this->string_replace($this->color);
        $this->discribe=$this->string_replace($this->discribe);
        $this->sourceId=$this->string_replace($this->sourceId);
    }
    public function returnData()
    {
        $this->result=array('factoryId'=>$this->sourceId,'name'=>$this->name,'size'=>$this->size,'color'=>$this->color,'price'=>$this->price,'discribe'=>$this->discribe,'url'=>$this->picture);
        $this->ajaxReturn();
    }
    public function ajaxReturn()
    {
        //echo json_encode(array('info'=>'ok','status'=>1,'data'=>$this->result));
        var_dump($this->result);
    }
}                                                                                           //利用phpquery的爬虫

function getFileExtName($path)                                                                //返回文件的扩展名
{
        $name = strrchr($path,'.');                                                                    //找到最后一个.
        $pos = (strpos($name,'?')? strpos($name,'?'):strpos($name,'/'));    // 判断.后面有没有根参数
        if($pos) return substr($name,1,$pos-1);
        else return substr($name,1);
}

function quickSort(&$array)                                                                       //快速排序法
{
    if (count($array)<=1) return $array;                                                       //当只有三个数比较时就不用继续递归了
    else
    {
        $index=floor(count($array)/2);
        $middle=array_splice($array,$index,1);                                              //取得中间数，这是一个数组只有中间数一个元素
        $left=array();                                                                                          //此时原始数组已被改变
        $right=array();                                                                                       //每次递归都重新生成这两个“左右数组”
        for($len=count($array),$i=0;$i<$len;$i++)
        {
            if($array[$i]>=$middle[0]) array_push($right,$array[$i]);
            else array_push($left,$array[$i]);
        }
        return array_merge(quickSort($left),$middle,quickSort($right));   //对左右数组继续排序
    }
}

function findKing($m,$n)
{
    $monkey = range(1,$n);
    $i=0;
    while (count($monkey)>1)
    {
        $i++;
        $head=array_shift($monkey);
        if($i%$m != 0) array_push($monkey,$head);
    }

    return $monkey[0];
}                                                                        //寻找猴王

function arraySort($array,$field)                                                                //二维数组升序排序
{
    $keySort=array();
    foreach ($array as $key=>$value)
    {
        $keySort[$key]=$value[$field];                                                            //将原数组的键值和需要比较的字段值拿出来组成新的数组
    }
    asort($keySort);                                                                                         //排序保留键值
    $new=array();
    foreach ($keySort as $key=>$value)
    {
        $new[$key]=$array[$key];
    }

    return $new;                                                                                              //返回排序的结果
}

function qrcode($value)
{
    include_once "phpqrcode/phpqrcode.php";                                        //二维码内容
    $errorCorrectionLevel = 'L';                                                                      //容错级别
    $matrixPointSize = 6;                                                                                //生成图片大小
    $qrPath="img/qrcode.png";                                                                    //生成的二维码的保存地址
    $logoPath='logo.png';                                                                              //准备好的logo图片
    QRcode::png($value, $qrPath, $errorCorrectionLevel, $matrixPointSize, 2,true);
    if ($logoPath !== FALSE) {
        $QR = imagecreatefromstring(file_get_contents($qrPath));
        $logoPath = imagecreatefromstring(file_get_contents($logoPath));
        $QR_width = imagesx($QR);                                                               //二维码图片宽度
        $QR_height = imagesy($QR);                                                              //二维码图片高度
        $logo_width = imagesx($logoPath);                                                   //logo图片宽度
        $logo_height = imagesy($logoPath);                                                  //logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        imagecopyresampled($QR, $logoPath, $from_width, $from_width, 0, 0, $logo_qr_width,  //重新组合图片并调整大小
            $logo_qr_height, $logo_width, $logo_height);
    }
    imagepng($QR, 'helloweba.png');                                                           //输出图片(二维码和logo的合成体)
    echo '<img src="helloweba.png">';
}                                                                         //利用phpqrcode生成二维码

