<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<!DOCTYPE html>
<head>
<title>KH Live!</title>
<link rel="stylesheet" type="text/css" href="<?PHP echo $site_css_default;?>" media="all" />
<link rel="stylesheet" type="text/css" href="<?PHP echo $site_css_mobile;?>" media="only screen and (max-width:840px)" />
<link type="text/css" rel="stylesheet" href="<?PHP echo $site_css;?>" media="only screen and (min-width:841px)" />
<meta name="viewport" content="width=320" />
<?PHP
if($page=="listening"){
if(strstr($_SESSION['meeting_status'],"live") OR $_SERVER['HTTP_HOST']=="127.0.0.1:8081" ){// for testing we trick it to belive it's live
}else{
 echo '<meta http-equiv="refresh" content="15" />';
}
}
?>
</head>
<body>
<div id="lines"></div>
<div id="title3">KH</div><div id="title4">Live!</div><div id="title_mobile2">mobile</div>
<div id="logout"><a href="./logout"><?PHP echo $lng['logout'];?></a></div>
<div id="live">
<?PHP
//we dont need to check if the server is live. If the page is displayed => the server is live.
/*
if (isset($_SESSION['time_counter'])){
	if ($_SESSION['time_counter']+$timer<=time()){
	$check_server=1;
	}else{
	$check_server=0;
	}
}else{
$_SESSION['time_counter']=time();
$check_server=1;
}

if ($check_server==1){
$_SESSION['time_counter']=time();
$fp = fsockopen( $server, $port , $errno, $errstr, 1); //last param is timeout 1sec
if (!$fp) {
	$_SESSION['server_status']="ko";
} else {
	fclose($fp);*/
	$_SESSION['server_status']="ok";/*
}

}
	if ($_SESSION['server_status']=="ok"){*/
	echo '<u>'.$version.'</u>';
	/*}else{
	echo '<i>'.$lng['serverko'].'</i>';
	}*/

?>
</div>
<div id="menu"><a href="./login"><?PHP echo $lng['home'];?></a>
 <?PHP
 if ($_SESSION['type']=="user") echo ' - <a href="./listening">'.$lng['listening'].'</a> - <a href="./record">'.$lng['recordings'].'</a>';
if ($_SESSION['type']=="manager") echo ' - <a href="./meeting">'.$lng['meeting'].'</a> - <a href="./record">'.$lng['recordings'].'</a> - <a href="./report">'.$lng['report'].'</a>';
if ($_SESSION['type']=="admin") echo ' - <a href="./listening">'.$lng['listening'].'</a> - <a href="./record">'.$lng['recordings'].'</a> - <a href="./meeting">'.$lng['meeting'].'</a> - <a href="./users">'.$lng['users'].'</a> - <a href="./report">'.$lng['report'].'</a>';
if ($_SESSION['type']=="root") echo ' - <a href="./listening">'.$lng['listening'].'</a> - <a href="./record">'.$lng['recordings'].'</a> - <a href="./download">'.$lng['file_transfer'].'</a> - <a href="./meeting">'.$lng['meeting'].'</a> - <a href="./diagnosis">Diagnosis</a> - <a href="./users">'.$lng['users'].'</a> - <a href="./congregations">'.$lng['congregations'].'</a> - <a href="./logs">'.$lng['logs'].'</a> - <a href="./report">'.$lng['report'].'</a>';
?>
</div>


