<?PHP
include 'db/config.php';
if (isset($_GET['show'])){
	if ($_GET['show']==1){
	//show the overlay in vmix
	
$url = 'http://'.$vmix_url.'/api';
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}

curl_close($ch);
$old_over_pos='';
$result_temp=explode('" type="Browser" title="Browser',$result);
if (isset($result_temp[1])){
$old_over_pos=substr($result_temp[0],-1);
}
if ($old_over_pos==''){
$result_temp=explode('</inputs>',$result);
if (strstr($result_temp[0], 'number="4"')){
die('no_space_left');
}
//the browser input doesn't exist we must add it first
$url = 'http://'.$vmix_url.'/api?function=AddInput&value=Browser'.urlencode('|http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/kh-live/timing-vmix.php');
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);
$url = 'http://'.$vmix_url.'/api';
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}

curl_close($ch);
$old_over_pos='';
$result_temp=explode('" type="Browser" title="Browser',$result);
if (isset($result_temp[1])){
$old_over_pos=substr($result_temp[0],-1);
}else{
die('couldnt load overlay');
}
}
$url = 'http://'.$vmix_url.'/api?function=SetAlpha&value=125&input='.$old_over_pos;
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);

$url = 'http://'.$vmix_url.'/api?function=SetPanY&value=-1.5&input='.$old_over_pos;
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);

$url = 'http://'.$vmix_url.'/api?function=OverlayInput1&input='.$old_over_pos;
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);

	exit();
	}
}
if (isset($_GET['hide'])){
	if ($_GET['hide']==1){
	//hide the overlay in vmix
	
$url = 'http://'.$vmix_url.'/api?function=OverlayInput1Out';
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);
$url = 'http://'.$vmix_url.'/api';
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}

curl_close($ch);
$old_over_pos='';
$result_temp=explode('" type="Browser" title="Browser',$result);
if (isset($result_temp[1])){
$old_over_pos=substr($result_temp[0],-1);
}else{
die('couldnt load overlay');
}
$url = 'http://'.$vmix_url.'/api?function=RemoveInput&input='.$old_over_pos;
$ch = curl_init();

curl_setopt($ch,CURLOPT_HTTPHEADER, array('User-Agent : PHP'));
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);
	exit();
	}
}
?>
<html>
<head>
<link rel="manifest" href="/manifest.php">
</head>
<body style="background-color:black;">
<script type="text/javascript">
function refreshPage(){
window.location='./timing-vmix.php';
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
	top:40px;
	left:0;
	width:100%;
	background-color: black;
	margin:0;
	color:white;
	text-align:center;
}
#meeting_overall{
	background-color:black;
	padding:15px;
	text-align:center;
	font-size:calc(<?PHP echo @$timing_multi; ?> * 2em);
	display:inline-block;
	max-width: calc(<?PHP echo @$timing_multi; ?> * 800px);
}
#meeting_overall p{
	display:none;
}
#meeting_times, #meeting_clock{
	font-family: 'test1', sans-serif;
	text-align:center;
	font-size:calc(<?PHP echo @$timing_multi; ?> * 4em);
	display:inline-block;
	padding-left:30px;
	padding-right:30px;
	text-align:center;
}
#hours, #minutes, #secondes, #meeting_clock h1{
display:inline-block;
}
 #meeting_clock{
 color:rgba(255,255,255,0.05);
position:absolute;
 }
</style>
<?PHP
	include ("./meeting-time.php");
echo '</body>
	</html>';
die();
?>