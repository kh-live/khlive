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

$adding=cong_add($cong_name, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $voip_type, $answer, $stream_quality, $sip_caller_ip, $cong_no, $conf_admin, $conf_user);
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
<br /><br />
<b>Voip account type</b><br />
none : the congregation streams to the server with Edcast (currently not working).<br />
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
<b>Streaming quality</b><br />
<select name="stream_quality">
<option value="0" <?PHP if ($stream_quality=="0") echo 'selected=selected';?>>0 (15 kb/s - low quality)</option>
<option value="1" <?PHP if ($stream_quality=="1") echo 'selected=selected';?>>1 (18 kb/s)</option>
<option value="2" <?PHP if ($stream_quality=="2") echo 'selected=selected';?>>2 (21 kb/s)</option>
<option value="3" <?PHP if ($stream_quality=="3") echo 'selected=selected';?>>3 (24 kb/s - default)</option>
<option value="4" <?PHP if ($stream_quality=="4") echo 'selected=selected';?>>4 (27 kb/s)</option>
<option value="5" <?PHP if ($stream_quality=="5") echo 'selected=selected';?>>5 (30 kb/s)</option>
<option value="6" <?PHP if ($stream_quality=="6") echo 'selected=selected';?>>6 (33 kb/s)</option>
<option value="7" <?PHP if ($stream_quality=="7") echo 'selected=selected';?>>7 (36 kb/s)</option>
<option value="8" <?PHP if ($stream_quality=="8") echo 'selected=selected';?>>8 (39 kb/s)</option>
<option value="9" <?PHP if ($stream_quality=="9") echo 'selected=selected';?>>9 (41 kb/s)</option>
<option value="10" <?PHP if ($stream_quality=="10") echo 'selected=selected';?>>10 (43 kb/s - high quality)</option>
</select><br /><br />
<b>Stream type</b><br />
ogg: compatible with Chrome Firefox and Opera<br />
mp3 : compatible with IE Chrome Safari and Firefox (V.21+, Vista+).<br />
<select name="stream_type">
<option value="ogg" <?PHP if ($stream_type=="ogg") echo 'selected=selected';?>>ogg</option>
<option value="mp3" <?PHP if ($stream_type=="mp3") echo 'selected=selected';?>>mp3</option>
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