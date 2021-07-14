<?PHP
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$gen_version='2.3.3';
$max_song_no=154;
?>
<!DOCTYPE html>
<head>
<title>KH Live!</title>
<link rel="icon" sizes="144x144" href="./img/logo-small.png">
<style type="text/css">
<?PHP
include "./style.css";
?>
</style>
<script type="text/javascript">
function toogleDiv(id){
if (document.getElementById("subgroup" + id).style.display=="block"){
document.getElementById("subgroup" + id).style.display="";
}else{
document.getElementById("subgroup" + id).style.display="block";
}
}
</script>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
</head>
<body>
<div id="title3">KH</div><div id="title4">Live!</div>
<div id="live">
</div>
<div id="page">
<h2>Installation steps</h2>
<?PHP
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
$status='';
ob_start();
       
        echo '<?PHP
	/**last change on : '.date("F d Y H:i:s").'**/
' ;
?>
$version='2.3.3';
$max_song_no='154';
$server_beta='stream';
$server_user_group='asterisk:asterisk';
$server_in='localhost';
$server_out='<?PHP echo $_POST['server_out']; ?>';
$timer='60';
$timer_listen='15';
$auto_ppp='no';
$auto_gov='yes';
$auto_cron='yes';
$scheduler='yes';
$video_dowloader='no';
$auto_stop='yes';
$auto_dns='no';
$moo_adr='';
$moo_key='';
$auto_khlive='<?PHP echo $_POST['auto_khlive']; ?>';
$api_key='<?PHP echo $_POST['api_key']; ?>';
$master_key='AJp6jDu5RQ9Jmq';
$test_url='google.com';
$test_ip='192.168.1.1';
$max_stream_no='2';
$sound_quality='22050';
$encoder_speed='26';
$song_dev='jwapp';
$song_type='normal';
$song_quality='480';
$server_audio='0';
$alsa_in='default';
$alsa_out='default';
$vmix='no';
$vmix_url='';
$vmix_path='';
$vmix_song_path='';
$vmix_lib_path='';
$vmix_auto_pause='no';
$timing_conf='yes';
$timing_style='default';
$timing_font_size_1='1';
$timing_font_size_2='3';
$timing_multi='1';
$devel_account='yes';
$qpin_max='3';
$qpin_time='1';
$web_server_root='/var/www/html/';
$temp_dir='/dev/shm/';
$asterisk_bin='/usr/sbin/asterisk';
$asterisk_spool='/var/spool/asterisk/';
$lame_bin='/usr/bin/lame';
$ezstream_bin='/usr/bin/ezstream';
$ices_bin='/usr/bin/ices2';
$icecast_bin='icecast2';
$port='8000';
$ttl_back='5';
$ttl_front='3';
<?PHP
echo '?>';
    $message = ob_get_clean();
//we must first check that the db folder exists
if (!is_dir('./db')) mkdir('./db', 0750);
$file = fopen('./db/config.php', 'w');
            if (fwrite($file, $message)){
	               fclose ($file);
		       }else{
		       $status='ko';
		       }
		       
			$cong_name=$_POST['cong_name']; //check
			$cong_lang=$_POST['cong_lang']; //check
			$phone_no='';
			$voip_type=$_POST['voip_type'];
			$stream='yes';
			$stream_server='';
			$stream_type=$_POST['stream_type'];
			$voip_pwd=substr(str_shuffle('aABcCdDEeFfgGHhiIJjkKLlmMNnoOPpqQRrSstTUuvVWwxXYyzZ0123456789'),0, 16);
			$trunk='no';
			$record=$_POST['record'];
			$answer=$_POST['answer'];
			$stream_quality='3';
			$sip_caller_ip='';
			
$adding=cong_add($cong_name, $cong_lang, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $answer, $stream_quality, $sip_caller_ip);
if ($adding=='ok'){
$info=time().'**info**local cong add successful**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
}else{
$status='ko';
}
include("./db/config.php");
	$user=$_POST['user'];
	$password=$_POST['password'];
	$name=$_POST['name'];
	$congregation=$cong_name;
	$rights='root';
	$pin= rand(10000,99999);
	$type='all';
	$info=$_POST['info'];
	$last_login=time();
	$encode="1";
	$adding2=kh_user_add($user,$password,$name,$congregation,$rights,$pin,$type,$last_login,$info,$encode);
if ($adding2=='ok'){
$info=time().'**info**local user add successful**'.$user.'@'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
$status='ko';
}
	    if ($status==''){
            echo "<br /><b style=\"color:green;\">Configuration saved successfully! </b><a href=\"./\">Click here</a> and login with the user you just created." ;
	    }else{
		echo $adding."<br />".$adding2."<br /><b style=\"color:red;\">Error saving! </b><a href=\"./\">Click here</a> and try again." ;	    
		}
	
	}
}else{
?>
Complete these steps to get started with KH-Live :
<form action="./" method="post">
<div class="subgroup" onclick="javascript:toogleDiv(1)">1. Basic settings</div>
<div class="subgroups" id="subgroup1">
Do you want to link this server with kh-live.co.za ?<br />This provides automatic update of dynamic ip address at kh-live.co.za every 5min<br />and auto link up users changes with <b>kh-live.co.za</b> so that your users can use this address to login<br />
<select class="field_login" name="auto_khlive" >
<option value="yes">Yes</option>
<option value="no">No</option>
</select><br />
What is your server's fully qualified name ? <br />This is the address at which the server is reachable from the internet (f eg. my_server.kh-live.co.za).<br />
<input class="field_login" type="text" name="server_out" value="" /><br />
What is your kh-live API key ? <br />api key for link up with main server<br />
<input class="field_login" type="text" name="api_key" value="" /><br />

</div>
<div class="subgroup" onclick="javascript:toogleDiv(2)">2. Create a Congregation</div>
<div class="subgroups" id="subgroup2">
<b><?PHP echo $lng['congregation'];?></b><br />
No spaces allowed (use "_" instead).<br />
<input class="field_login" type="text" name="cong_name" />
<br /><b>Congregation language ID</b><br />
use the same language ID as on Jw.org (English=E Afrikaans=AF)<br />
<input class="field_login" type="text" name="cong_lang" />
<br /><br />
<b>Account type</b><br />
none : the congregation streams to the server with Edcast .<br />
Direct stream : Use the sound input/output on the server.<br />
<select name="voip_type">
<option value="none">none</option>
<option value="direct-stream">direct stream</option>
</select><br /><br />
<b>Stream type</b><br />
ogg: compatible with Chrome Firefox and Opera<br />
mp3 : compatible with IE Chrome Safari and Firefox (V.21+, Vista+).<br />
both : will stream in both formats at the same time. <br />
<select name="stream_type">
<option value="ogg">ogg</option>
<option value="mp3">mp3</option>
<option value="both">both</option>
</select><br /><br /><br />
<b>Record meetings</b><br />
<select name="record">
<option value="yes">yes</option>
<option value="no">no</option>
</select><br /><br />
<b>Enable answering</b><br />
<select name="answer">
<option value="yes">yes</option>
<option value="no">no</option>
</select><br /><br />
</div>
<div class="subgroup" onclick="javascript:toogleDiv(3)">3. Create an Administrator</div>
<div class="subgroups" id="subgroup3">
<b><?PHP echo $lng['name'];?></b><br />
User's real full name.<br />
<input class="field_login" type="text" name="name"><br /><br />
<b>Info</b><br />
Information about the user.<br />
<input class="field_login" type="text" name="info"><br /><br />
<b><?PHP echo $lng['user'];?></b><br />
User's account name (used to login)<br />
<input class="field_login" type="text" name="user"><br /><br />
<b><?PHP echo $lng['password'];?></b><br />
At least 8 characters. Tip : use a sentence!<br />
<input class="field_login" type="password" name="password"><br /><br />
</div>
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
<?PHP
}
?>
</div>
</body>
</html>
