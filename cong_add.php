<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include '404.php';
exit(); 
}
include 'functions.php';
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['cong_name']!=""){
		//we obviously need to check the input
			$cong_name=$_POST['cong_name']; //check
			$cong_lang=$_POST['cong_lang']; //check
			$phone_no=@$_POST['phone_no'];
			$voip_type=$_POST['voip_type'];
			$stream=$_POST['stream'];
			$stream_server=$_POST['stream_server'];
			$stream_type=$_POST['stream_type'];
			$voip_pwd=$_POST['voip_pwd'];
			$trunk=$_POST['trunk'];
			$record=$_POST['record'];
			$voip_type=$_POST['voip_type'];
			$answer=$_POST['answer'];
			$stream_quality=$_POST['stream_quality'];
			$sip_caller_ip=$_POST['sip_caller_ip'];
			
$adding=cong_add($cong_name, $cong_lang, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $voip_type, $answer, $stream_quality, $sip_caller_ip);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**local cong add successful**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**local cong add fail**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'...</div>';
		}
	} ?>
	<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
Click <a href="./congregations">here</a> to edit more congregations.<br /><br />
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
<?PHP echo $lng['add_new_congregation'];?><br /><br />
<form action="./cong_add" method="post">
<b><?PHP echo $lng['congregation'];?></b><br />
No spaces allowed (use "_" instead). One "_" and only one "_" required.<br />
<input class="field_login" type="text" name="cong_name" />
<br /><b>Congregation language ID</b><br />
use the same language ID as on Jw.org (English=E Afrikaans=AF)<br />
<input class="field_login" type="text" name="cong_lang" />
<br /><br />
<b>Account type</b><br />
none : the congregation streams to the server with Edcast .<br />
SIP : the congregation connects to the server with Jitsy.<br />
IAX : the congregation connects to the server with Yate (currently not working).<br />
Direct input : Use the sound input/output on the server. this requires USB stack to be limited to 1.1<br />
Direct stream : Use the sound input/output on the server only if voip is disabled.<br />
<select name="voip_type">
<option value="none">none</option>
<?PHP
if ($server_beta!='stream'){
?>
<option value="sip">SIP</option>
<option value="iax">IAX</option>
<option value="direct">direct input</option>
<?PHP
}else{
?>
<option value="direct-stream">direct stream</option>
<?PHP
}
?>
</select><br /><br />
<b>Voip account number (Phone no)</b><br />
<input class="field_login" type="text" name="phone_no" />
<br />
sip_caller_ip :<br />limit cong meeting call to this IP (leave blank if no limit required) <br />
<input class="field_login" type="text" name="sip_caller_ip"  /><br /><br />
<b>Enable streaming</b><br />
<select name="stream">
<option value="yes">yes</option>
<option value="no">no</option>
</select><br /><br />
<br />
stream_to_server :<br />server to send the stream to. usually localhost.<br />
<input class="field_login" type="text" name="stream_server"  /><br /><br />
<b>Streaming quality</b><br />
in the following format : OGG quality number (OGG bitrate estimate - MP3 bitrate)<br />
<select name="stream_quality">
<?PHP $multiplier= ($sound_quality / 8000);
$bitrates=array(
		'8000-0' => '8',
		'8000-1' => '16',
		'8000-2' => '24',
		'8000-3' => '32',
		'8000-4' => '40',
		'8000-5' => '48',
		'8000-6' => '56',
		'8000-7' => '64',
		'8000-8' => '64',
		'8000-9' => '64',
		'8000-10' => '64',
		'11025-0' => '8',
		'11025-1' => '16',
		'11025-2' => '24',
		'11025-3' => '32',
		'11025-4' => '40',
		'11025-5' => '48',
		'11025-6' => '56',
		'11025-7' => '64',
		'11025-8' => '64',
		'11025-9' => '64',
		'11025-10' => '64',
		'16000-0' => '16',
		'16000-1' => '24',
		'16000-2' => '32',
		'16000-3' => '40',
		'16000-4' => '48',
		'16000-5' => '56',
		'16000-6' => '64',
		'16000-7' => '80',
		'16000-8' => '96',
		'16000-9' => '112',
		'16000-10' => '128',
		'22050-0' => '24',
		'22050-1' => '32',
		'22050-2' => '40',
		'22050-3' => '48',
		'22050-4' => '56',
		'22050-5' => '64',
		'22050-6' => '80',
		'22050-7' => '96',
		'22050-8' => '112',
		'22050-9' => '128',
		'22050-10' => '144',
		'32000-0' => '32',
		'32000-1' => '40',
		'32000-2' => '48',
		'32000-3' => '56',
		'32000-4' => '64',
		'32000-5' => '80',
		'32000-6' => '96',
		'32000-7' => '112',
		'32000-8' => '128',
		'32000-9' => '160',
		'32000-10' => '192',
		'44100-0' => '32',
		'44100-1' => '40',
		'44100-2' => '48',
		'44100-3' => '56',
		'44100-4' => '64',
		'44100-5' => '80',
		'44100-6' => '96',
		'44100-7' => '112',
		'44100-8' => '128',
		'44100-9' => '160',
		'44100-10' => '192',
		'48000-0' => '32',
		'48000-1' => '40',
		'48000-2' => '48',
		'48000-3' => '56',
		'48000-4' => '64',
		'48000-5' => '80',
		'48000-6' => '96',
		'48000-7' => '112',
		'48000-8' => '128',
		'48000-9' => '160',
		'48000-10' => '192'
		);

$bitrate = (15 * $multiplier);
?>
<option value="0" >0 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-0']; ?> kb/s - low quality)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="1" >1 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-1']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="2" >2 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-2']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="3" selected="selected" >3 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-3']; ?> kb/s - default)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="4" >4 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-4']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="5" >5 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-5']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="6" >6 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-6']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="7" >7 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-7']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="8" >8 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-8']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="9" >9 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-9']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="10" >10 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-10']; ?> kb/s - high quality)</option>
</select><br /><br />
<b>Stream type</b><br />
ogg: compatible with Chrome Firefox and Opera<br />
mp3 : compatible with IE Chrome Safari and Firefox (V.21+, Vista+).<br />
both : will stream in both formats at the same time. Only in NO VOIP mode. Account type must be "direct stream" or "none".<br />
<select name="stream_type">
<option value="ogg">ogg</option>
<option value="mp3">mp3</option>
<option value="both">both</option>
</select><br /><br /><br />
<b>Congregation pwd (Voip/Stream)</b><br />
Generated automaticaly (do not use weaker password).<br />
<input class="field_login" type="text" name="voip_pwd" value="<?PHP
$i=16;
while ($i>=1){
echo substr(str_shuffle('aBcEeFgHiJkLmNoPqRstUvWxYz0123456789'),0, 1);
$i--;
}?>"/>
<br /><br />
<b>Enable trunking</b><br />
Allows access with a landline or cellphone. The trunk needs to be configured separetly.<br />
<select name="trunk">
<option value="yes">yes</option>
<option value="no">no</option>
</select><br /><br />
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
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP } ?>