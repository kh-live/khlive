<?PHP
$max_song_no=154;
if(session_id()==""){session_start();}
include "db/config.php";
if (isset($_GET['song_1'])){
	if ($_GET['song_1'] >=1 AND $_GET['song_1'] <=$max_song_no){
	$_SESSION['song_1']=$_GET['song_1'];
			$file=fopen($temp_dir.'song_1_'.$_SESSION['cong'],'w');
			if(fputs($file,$_GET['song_1'])){
			fclose($file);
			}
if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_1'].".mp3")){
 echo 'iasnm-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_1'].".mp3")){
	 echo 'snnw-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_1'].".mp3")){
	 echo 'iasn-mp3';
	}else{
	 echo 'iasn-m4a';
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
	if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_2'].".mp3")){
 echo 'iasnm-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_2'].".mp3")){
	 echo 'snnw-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_2'].".mp3")){
	 echo 'iasn-mp3';
	}else{
	 echo 'iasn-m4a';
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
	if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_3'].".mp3")){
 echo 'iasnm-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_3'].".mp3")){
	 echo 'snnw-mp3';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_3'].".mp3")){
	 echo 'iasn-mp3';
	}else{
	 echo 'iasn-m4a';
	}
	}
}
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
?>