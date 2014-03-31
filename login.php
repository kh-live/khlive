<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<html>
<head>
<title><?PHP echo $lng['signin'];?></title>
<link rel="stylesheet" type="text/css" href="<?PHP echo $site_css_default;?>" media="all" />
<link rel="stylesheet" type="text/css" href="<?PHP echo $site_css_mobile;?>" media="only screen and (max-width:840px)" />
<link type="text/css" rel="stylesheet" href="<?PHP echo $site_css;?>" media="only screen and (min-width:841px)" />
<meta name="viewport" content="width=320" />
</head>
<body>
<div id="titles">
<div id="title1">KH</div>
<div id="title2">Live!</div>
<div id="title_mobile">mobile</div>
</div>
<div id="login">
<form action="./login" method="post">
<b><?PHP echo $lng['user'];?></b><br />
<input class="field_login" type="text" name="login_user"><br />
<b><?PHP echo $lng['password'];?></b><br />
<input class="field_login" type="password" name="login_password"><br />
<input id="input_login" type="submit" value="<?PHP echo $lng['signin'];?>">
</form>
</div>
<div id="login-message"><?PHP echo $login_error;?></div>
</body>
</html>