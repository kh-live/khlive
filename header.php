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
<style type="text/css">
<?PHP
include "./style.css";
?>
</style>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
<?PHP
if($page=="listening"){
if(strstr($_SESSION['meeting_status'],"live") OR $server_beta=="true" ){// for testing we trick it to belive it's live
}else{
 echo '<meta http-equiv="refresh" content="'.$timer_listen.'" />';
}
}
?>
</head>
<body>
<div id="title3">KH</div><div id="title4">Live!</div>
<div id="live">
<?PHP
//we dont need to check if the server is live. If the page is displayed => the server is live.
//is server_status still used anywhere?
$_SESSION['server_status']="ok";
	echo '<u>'.$version.'</u>';
?>
</div>

<div id="hide_menu" onclick="javascript:hideMenu()">&#9776;</div>
<div id="menu"><a href="./login"><?PHP echo $lng['home'];?></a>
 <?PHP
 if ($_SESSION['type']=="user"){
 echo '<a href="./listening">'.$lng['listening'].'</a>
 <a href="./record">'.$lng['recordings'].'</a>';
if ($server_beta!="master") echo '<a href="./video">Videos</a>';
}
if ($_SESSION['type']=="manager" AND $server_beta!="master"){

echo '<a href="./meeting">'.$lng['meeting'].'</a>
<a href="./record">'.$lng['recordings'].'</a>
<a href="./report">'.$lng['report'].'</a>';
	if(@$scheduler=='yes'){
	echo '<a href="./scheduler">Scheduler</a>';
	}
echo '<a href="./video">Videos</a>';
}
if ($_SESSION['type']=="admin"){
if ($server_beta!="master") {
echo '<a href="./diagnosis">Diagnosis</a>';
}
echo '<a href="./download">'.$lng['file_transfer'].'</a>
<a href="./listening">'.$lng['listening'].'</a>';
if ($server_beta!="master") {
echo '<a href="./meeting">'.$lng['meeting'].'</a>';
echo '<a href="./infos">Notice Board</a>';
}
echo '<a href="./record">'.$lng['recordings'].'</a>';
if ($server_beta!="master") {
echo '<a href="./report">'.$lng['report'].'</a>';
	if(@$scheduler=='yes'){
	echo '<a href="./scheduler">Scheduler</a>';
	}
echo '<a href="./users">'.$lng['users'].'</a>
<a href="./video">Videos</a>
';
}else{
echo '<a href="./users">'.$lng['users'].'</a>';
}
}
if ($_SESSION['type']=="root"){
echo '<a href="./configure">Configuration</a>
<a href="./congregations">'.$lng['congregations'].'</a>
<a href="./diagnosis">Diagnosis</a>
<a href="./download">'.$lng['file_transfer'].'</a>
<a href="./listening">'.$lng['listening'].'</a>
<a href="./logs">'.$lng['logs'].'</a>';
if ($server_beta!="master") {
echo '<a href="./song_man">Manage Songs</a>';
echo '<a href="./meeting">'.$lng['meeting'].'</a>';
echo '<a href="./infos">Notice Board</a>';
}
echo '<a href="./record">'.$lng['recordings'].'</a>';
if ($server_beta!="master") {
	echo '<a href="./report">'.$lng['report'].'</a>';
	if(@$scheduler=='yes'){
	echo '<a href="./scheduler">Scheduler</a>';
	}
echo '
<a href="./users">'.$lng['users'].'</a>
<a href="./video">Videos</a>
';
}else{
echo ' <a href="./users">'.$lng['users'].'</a>
<a href="./servers">Servers</a>';
}
}
?>
<a  id="logout" href="./logout"><?PHP echo $lng['logout'];?></a>
</div>

