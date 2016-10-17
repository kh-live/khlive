<?PHP
if(session_id()==""){session_start();}
include "db/config.php";
if (isset($_GET['song_1'])){
	if ($_GET['song_1'] >=1 AND $_GET['song_1'] <=$max_song_no){
	$_SESSION['song_1']=$_GET['song_1'];
			$file=@fopen($temp_dir.'song_1_'.$_SESSION['cong'],'w');
			if(@fputs($file,$_GET['song_1'])){
			fclose($file);
			}
if ($song_type=='normal'){
if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_1'].".mp3")){
 echo 'iasnm-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_1'].".mp3")){
	 echo 'snnw-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_1'].".mp3")){
	 echo 'iasn-mp3';
	}else{
	 echo 'iasn-m4a';
	}
	
}elseif($song_type=='joy'){
if (is_file($web_server_root."kh-live/kh-songs/sjjm_E_".$_SESSION['song_1'].".mp3")){
 echo 'sjjmm-mp3';
 }
}else{
//vmix
}
}
}
if (isset($_GET['song_2'])){
	if ($_GET['song_2'] >=1 AND $_GET['song_2'] <=$max_song_no){
	$_SESSION['song_2']=$_GET['song_2'];
	$file=fopen($temp_dir.'song_2_'.$_SESSION['cong'],'w');
			if(fputs($file,$_GET['song_2'])){
			fclose($file);
			}
if ($song_type=='normal'){
	if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_2'].".mp3")){
 echo 'iasnm-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_2'].".mp3")){
	 echo 'snnw-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_2'].".mp3")){
	 echo 'iasn-mp3';
	}else{
	 echo 'iasn-m4a';
	}
	
}elseif($song_type=='joy'){
if (is_file($web_server_root."kh-live/kh-songs/sjjm_E_".$_SESSION['song_2'].".mp3")){
 echo 'sjjmm-mp3';
 }
}else{
//vmix
}
}
}
if (isset($_GET['song_3'])){
	if ($_GET['song_3'] >=1 AND $_GET['song_3'] <=$max_song_no){
	$_SESSION['song_3']=$_GET['song_3'];
	$file=fopen($temp_dir.'song_3_'.$_SESSION['cong'],'w');
			if(fputs($file,$_GET['song_3'])){
			fclose($file);
			}
if ($song_type=='normal'){
	if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_3'].".mp3")){
 echo 'iasnm-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_3'].".mp3")){
	 echo 'snnw-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_3'].".mp3")){
	 echo 'iasn-mp3';
	}else{
	 echo 'iasn-m4a';
	}
	
}elseif($song_type=='joy'){
if (is_file($web_server_root."kh-live/kh-songs/sjjm_E_".$_SESSION['song_3'].".mp3")){
 echo 'sjjmm-mp3';
 }
}else{
//vmix
}
}
}
if ($song_dev=='server'){
if (isset($_GET['play'])){
	if (is_numeric($_GET['play'])){
	if ($_GET['play'] >=1 AND $_GET['play'] <=$max_song_no) {
	exec("/usr/bin/mocp -x");
	exec("/usr/bin/mocp -S");
	if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_GET['play'].".mp3")){
	exec("/usr/bin/mocp -l ".$web_server_root."kh-live/kh-songs/iasnm_E_".$_GET['play'].".mp3");
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_GET['play'].".mp3")){
	exec("/usr/bin/mocp -l ".$web_server_root."kh-live/kh-songs/snnw_E_".$_GET['play'].".mp3");
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_GET['play'].".mp3")){
	exec("/usr/bin/mocp -l ".$web_server_root."kh-live/kh-songs/iasn_E_".$_GET['play'].".mp3");
	}else{
	exec("/usr/bin/mocp -l ".$web_server_root."kh-live/kh-songs/iasn_E_".$_GET['play'].".m4a");
	}
	if(strstr($_SESSION['meeting_status'],"live")){
	echo "Playing...";
	}else{
	echo "not_live";
	}
	}
	}elseif ($_GET['play']=="rand"){
	exec("/usr/bin/mocp -x");
	echo "Playing Random...";
	exec("/usr/bin/mocp -S");
	exec("/usr/bin/mocp -c");
	exec("/usr/bin/mocp -a ".$web_server_root."kh-live/kh-songs/");
	exec("/usr/bin/mocp -t shuffle");
	exec("/usr/bin/mocp -p");
	}
}
if (isset($_GET['stop'])){
	
	if ($_GET['stop']=="true") {
	exec("/usr/bin/mocp -x");
	
	echo "Stopped...";
	}
}
if (isset($_GET['pause'])){
	
	if ($_GET['pause']=="true") {
	exec("/usr/bin/mocp -G");
	echo "Paused...";
	}
}
}elseif ($song_dev=='vmix'){
if (isset($_GET['play'])){
	if (is_numeric($_GET['play'])){
	if ($_GET['play'] >=1 AND $_GET['play'] <=$max_song_no) {
	if ($song_type=='normal'){
	$song_temp_name="iasnm_E_".$_GET['play'].".mp3";
	$song_temp_type='AudioFile';
	$song_temp='iasnm_E_';
	}
	if ($song_type=='joy') {
	$song_temp_name="sjjm_E_".$_GET['play'].".mp3";
	$song_temp_type='AudioFile';
	$song_temp='sjjm_E_';
	}
	if ($song_type=='vid') {
	$song_temp_name="sjjv_E_".$_GET['play'].".mp4";
	$song_temp_type='Video';
	$song_temp='sjjv_E_';
	}
	$urls=array();
	$song_temp_pos='';
	//what if all the inputs are full?
//if there is already a song, we close it first
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

$result_temp=explode('" type="AudioFile" title="'.$song_temp,$result);
if (isset($result_temp[1])){
$old_song_pos=substr($result_temp[0],-1);
}
if (isset($old_song_pos)){
$url = 'http://'.$vmix_url.'/api?function=RemoveInput&input='.$old_song_pos;
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
}else{
$result_temp=explode('</inputs>',$result);
if (strstr($result_temp[0], 'number="4"')){
die('no_space_left');
}
}

curl_close($ch);
$url = 'http://'.$vmix_url.'/api?function=AddInput&value='.$song_temp_type.urlencode('|'.$vmix_path.$song_temp_name);
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
//we check which position the song did load at
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

$result_temp=explode('" type="AudioFile" title="'.$song_temp_name,$result);
$song_temp_pos=substr($result_temp[0],-1);
echo $song_temp_pos;
curl_close($ch);
//we set the song in 2nd position
$url = 'http://'.$vmix_url.'/api?function=MoveInput&value=2&input='.$song_temp_pos;
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
sleep(1);
//we activate the song
$url = 'http://'.$vmix_url.'/api?function=ActiveInput&input=2';
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
//we play the song

$url = 'http://'.$vmix_url.'/api?function=Play&input=2';
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
//we put the yeartext back
//this must be done in a ajax call or something WaitForCompletion keeps the socket open until the song is finished but php times out.
/*
$url = 'http://'.$vmix_url.'/api?function=WaitForCompletion&input=2';
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

$url = 'http://'.$vmix_url.'/api?function=ActiveInput&input=1';
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
*/	
	}
	}
	}
}
?>
