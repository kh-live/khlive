<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include 'functions.php';

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['cong_confirmed']!=""){
		//start cong del
			$cong_confirmed=urldecode($_POST['cong_confirmed']);//sanitize
$deleting=cong_del($cong_confirmed, "edit");
if ($deleting=='ok'){
			$cong_name=$_POST['cong_name']; //check
			$cong_lang=$_POST['cong_lang']; //check
			$cong_no=$_POST['cong_no'];//what's that?
			$conf_admin=$_POST['conf_admin'];
			$conf_user=$_POST['conf_user'];
			$phone_no=$_POST['phone_no'];
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

$adding=cong_add($cong_name, $cong_lang, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $voip_type, $answer, $stream_quality, $sip_caller_ip, $cong_no, $conf_admin, $conf_user);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**local cong edit successful**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**local cong edit add fail**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
}else{
echo $deleting;
$info=time().'**error**local cong edit del fail**'.$cong_confirmed."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}	
			
		}else{
		echo '<div id="error_msg">'.$lng['error'].'16</div>';
		}
	}
}
if(isset($_GET['cong'])){
$cong_name=urldecode($_GET['cong']); //sanitize input
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong_name) {
	$cong_lang=@$data[15];
	$cong_no=$data[1];
	$conf_admin=$data[2];
	$conf_user=$data[3];
	$phone_no=@$data[4];
	$voip_type=@$data[5];
	$stream=@$data[6];
	$stream_type=@$data[7];
	$voip_pwd=@$data[8];
	$trunk=@$data[9];
	$record=@$data[10];
	$answer=@$data[11];
	$stream_quality=@$data[12];
	$sip_caller_ip=@$data[13];
	$stream_server=@$data[14];
	}
	}
	if ($voip_pwd==""){
				$i=16;
				while ($i>=1){
				$voip_pwd.=substr(str_shuffle('aBcEeFgHiJkLmNoPqRstUvWxYz0123456789'),0, 1);
				$i--;
				}
			}
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
Use the form bellow to edit the congregation<br /><br />
<form action="./cong_edit" method="post">
<b><?PHP echo $lng['congregation'];?></b><br />
No spaces allowed (use "_" instead). One "_" and only one "_" required.<br />
<input class="field_login" type="text" name="cong_name" value="<?PHP echo $cong_name;?>"/>
<br /><b>Congregation language ID</b><br />
use the same language ID as on Jw.org (English=E Afrikaans=AF).<br />
<input class="field_login" type="text" name="cong_lang" value="<?PHP echo $cong_lang;?>"/>
<br /><br />
<b>Account type</b><br />
none : the congregation streams to the server with Edcast.<br />
SIP : the congregation connects to the server with Jitsy.<br />
IAX : the congregation connects to the server with Yate.<br />
Direct input : Use the sound input/output on the server. this requires USB stack to be limited to 1.1<br />
Direct stream : Use the sound input/output on the server only if voip is disabled.<br />
<select name="voip_type">
<option value="none" <?PHP if ($voip_type=="none") echo 'selected=selected';?>>none</option>
<?PHP
if ($server_beta!='stream'){
?>
<option value="sip" <?PHP if ($voip_type=="sip") echo 'selected=selected';?>>SIP</option>
<option value="iax" <?PHP if ($voip_type=="iax") echo 'selected=selected';?>>IAX</option>
<option value="direct" <?PHP if ($voip_type=="direct") echo 'selected=selected';?>>direct input</option>
<?PHP
}else{
?>
<option value="direct-stream" <?PHP if ($voip_type=="direct-stream") echo 'selected=selected';?>>direct stream</option>
<?PHP
}
?>
</select><br /><br />
<b>Voip account number (Phone no)</b><br />
<input class="field_login" type="text" name="phone_no" value="<?PHP echo $phone_no;?>" />
<br />
sip_caller_ip :<br />limit cong meeting call to this IP (leave blank if no limit required) <br />
<input class="field_login" type="text" name="sip_caller_ip" value="<?PHP echo $sip_caller_ip;?>" /><br /><br />
<b>Enable streaming</b><br />
<select name="stream">
<option value="yes" <?PHP if ($stream=="yes") echo 'selected=selected';?>>yes</option>
<option value="no" <?PHP if ($stream=="no") echo 'selected=selected';?>>no</option>
</select><br /><br />
stream_to_server :<br />server to send the stream to. usually localhost.<br />
<input class="field_login" type="text" name="stream_server" value="<?PHP echo @$stream_server;?>" /><br /><br />
<b>Streaming quality @ <?PHP echo $sound_quality; ?> Hz</b><br />
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
<option value="0" <?PHP if ($stream_quality=="0") echo 'selected=selected';?>>0 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-0']; ?> kb/s - low quality)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="1" <?PHP if ($stream_quality=="1") echo 'selected=selected';?>>1 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-1']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="2" <?PHP if ($stream_quality=="2") echo 'selected=selected';?>>2 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-2']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="3" <?PHP if ($stream_quality=="3") echo 'selected=selected';?>>3 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-3']; ?> kb/s - default)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="4" <?PHP if ($stream_quality=="4") echo 'selected=selected';?>>4 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-4']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="5" <?PHP if ($stream_quality=="5") echo 'selected=selected';?>>5 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-5']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="6" <?PHP if ($stream_quality=="6") echo 'selected=selected';?>>6 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-6']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="7" <?PHP if ($stream_quality=="7") echo 'selected=selected';?>>7 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-7']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="8" <?PHP if ($stream_quality=="8") echo 'selected=selected';?>>8 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-8']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="9" <?PHP if ($stream_quality=="9") echo 'selected=selected';?>>9 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-9']; ?> kb/s)</option>
<?PHP $bitrate += (2 * $multiplier); ?>
<option value="10" <?PHP if ($stream_quality=="10") echo 'selected=selected';?>>10 (<?PHP echo round($bitrate).'kb/s - '.$bitrates[$sound_quality.'-10']; ?> kb/s - high quality)</option>
</select><br /><br />
<b>Stream type</b><br />
ogg: compatible with Chrome Firefox and Opera<br />
mp3 : compatible with IE Chrome Safari and Firefox (V.21+, Vista+).<br />
both : will stream in both formats at the same time. Only in NO VOIP mode. Account type must be "direct stream" or "none".<br />
<select name="stream_type">
<option value="ogg" <?PHP if ($stream_type=="ogg") echo 'selected=selected';?>>ogg</option>
<option value="mp3" <?PHP if ($stream_type=="mp3") echo 'selected=selected';?>>mp3</option>
<?PHP
if (($server_beta=='stream' AND $voip_type=="direct-stream") OR ($voip_type=="none")){
?>
<option value="both" <?PHP if ($stream_type=="both") echo 'selected=selected';?>>both</option>
<?PHP
}
?>
</select><br /><br /><br />
<b>Congregation pwd (Voip/Stream)</b><br />
Generated automaticaly (do not use weaker password).<br />
<input class="field_login" type="text" name="voip_pwd" value="<?PHP echo $voip_pwd;?>"/>
<br /><br />
<b>Enable trunking</b><br />
Allows access with a landline or cellphone. The trunk needs to be configured separetly.<br />
<select name="trunk">
<option value="yes" <?PHP if ($trunk=="yes") echo 'selected=selected';?>>yes</option>
<option value="no" <?PHP if ($trunk=="no") echo 'selected=selected';?>>no</option>
</select><br /><br />
<b>Congregation ID</b><br />
<input class="field_login" type="text" name="cong_no" value="<?PHP echo $cong_no;?>" />
<br /><br />
<b>Conf Admin PIN </b><br />
<input class="field_login" type="text" name="conf_admin" value="<?PHP echo $conf_admin;?>" />
<br /><br />
<b>Conf User PIN </b><br />
<input class="field_login" type="text" name="conf_user" value="<?PHP echo $conf_user;?>" />
<br /><br />
<b>Record meetings</b><br />
<select name="record">
<option value="yes" <?PHP if ($record=="yes") echo 'selected=selected';?>>yes</option>
<option value="no" <?PHP if ($record=="no") echo 'selected=selected';?>>no</option>
</select><br /><br />
<b>Enable answering</b><br />
<select name="answer">
<option value="yes" <?PHP if ($answer=="yes") echo 'selected=selected';?>>yes</option>
<option value="no" <?PHP if ($answer=="no") echo 'selected=selected';?>>no</option>
</select><br /><br />
<input type="hidden" name="cong_confirmed" value="<?PHP echo $cong_name;?>">
<a href="./congregations"><?PHP echo $lng['cancel'];?></a> <input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
Click <a href="./congregations">here</a> to edit more congregations.<br /><br />
</div>
<?PHP
}
?>