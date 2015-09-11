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
			
$adding=cong_add($cong_name, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $voip_type, $answer, $stream_quality, $sip_caller_ip);
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
<br /><br />
<b>Voip account type</b><br />
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
<select name="stream_quality">
<option value="0">0 (15 kb/s)</option>
<option value="1">1 (18 kb/s)</option>
<option value="2" >2 ( kb/s)</option>
<option value="3" <?PHP echo 'selected=selected';?>>3 ( kb/s - default)</option>
<option value="4">4 ( kb/s)</option>
<option value="5" >5 ( kb/s)</option>
<option value="6" >6 ( kb/s)</option>
<option value="7" >7 ( kb/s)</option>
<option value="8" >8 ( kb/s)</option>
<option value="9" >9 ( kb/s)</option>
<option value="10" >10 ( kb/s)</option>
</select><br /><br />
<b>Stream type</b><br />
ogg: compatible with Chrome Firefox and Opera<br />
mp3 : compatible with IE Chrome Safari and Firefox (V.21+, Vista+). Unstable...<br />
<select name="stream_type">
<option value="ogg">ogg</option>
<option value="mp3">mp3</option>
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