<?PHP
if ($server_beta=='master'){
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
}
?>
<html>
<head>
<title>KH Live! :: Timing</title>
<link rel="manifest" href="/manifest.php">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
<meta name="theme-color" content="#000000"/>
<link rel="icon" sizes="192x192" href="/img/time-small.png">
</head>
<body style="background-color:black;">
<script type="text/javascript">
function refreshPage(){
window.location='./time';
}
</script>
<style type="text/css">
@font-face{
	font-family: test1;
	src:url('./fonts/digital-7-mono.ttf');
}
@font-face{
	font-family: test2;
	src:url('./fonts/Orbitron-Black.ttf');
}
#meeting_time{
	font-family: 'test2', sans-serif;
	position:absolute;
	top:40px;
	left:0;
	width:100%;
	background-color: black;
	margin:0;
	color:white;
}
#meeting_overall{
	background-color:black;
	width:100%;
	padding-top:30px;
	padding-bottom:30px;
	text-align:center;
	font-size:1em;
}
#meeting_times, #meeting_clock{
	font-family: 'test1', sans-serif;
	text-align:center;
	font-size:3em;
}
#hours, #minutes, #secondes, #meeting_clock h1{
display:inline-block;
}
 #meeting_clock{
 color:rgba(255,255,255,0.05);
position:absolute;
width:100%;
 }
</style>
<?PHP
if ($server_beta=='master'){
if (isset($_GET['cong'])){
	$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3], $_GET['cong'])){
	$url_cong=$data[0];
	if ($_SERVER['REMOTE_ADDR']==$data[1]){
	$url_cong=$data[4];
	if (!filter_var($url_cong, FILTER_VALIDATE_IP) {
	echo ' Please configure your local ip address on kh-live correctly then try again.';
	die();
	}
	}
	}
  }
}
if (isset($url_cong)){
	//we must include the remote timing related to that cong
	echo '<script type="text/javascript">
window.location="http://'.$url_cong.'/kh-live/time";
</script>';
	}else{
	?>
	<script type="text/javascript">
	if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/service_worker_js.php').then(function(registration) {
      // Registration was successful
      console.log('ServiceWorker registration successful with scope: ', registration.scope);
    }, function(err) {
      // registration failed :(
      console.log('ServiceWorker registration failed: ', err);
    });
  });
}
function updateCong(){
var e=document.getElementById("cong");
var value = e.options[e.selectedIndex].value;
window.location='./time?cong=' + value;
}
</script>
	<?PHP
	echo '<h1 style="color:white;" >Select your cong :</h1><br />
	<select id="cong" name="cong" onchange="javascript:updateCong()">
<option value="0">'.$lng['select'].'...</option>';
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<option value="'.$data[0].'">'.$data[0].'</option>';
	}
echo '</select><br /><br />';
	}
	}else{
	include ("./meeting-time.php");
	}
echo '</body>
	</html>';
die();
?>