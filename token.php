<meta charset="UTF-8">
<?php
session_start();
if($_POST)
{
    if($_SESSION['token']==$_POST['token'])
    {
        echo "合法用户";
    }
    else echo "非法用户";
}
else
{
    $token=md5(uniqid(rand(),true));
    $_SESSION['token']=$token;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>form</title>
</head>
<body>
<form action="1.php" method="post">
    url:<input type="text"   name="urlist" />
    <input type="hidden" name="token" value="<?php echo $token;?>" />
    <br />
    <input type="submit" value="tijiao" />
</form>
</body>
</html>

