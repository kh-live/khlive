<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}


if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['cong_name']!=""){
		$error="";
		$error_stream="";
			$db=file("db/cong");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$_POST['cong_name']) $error="ko";
			}
			$cong_name=$_POST['cong_name']; //check
			$phone_no=@$_POST['phone_no'];
			$voip_type=$_POST['voip_type'];
			$stream=$_POST['stream'];
			$stream_type=$_POST['stream_type'];
			$voip_pwd=$_POST['voip_pwd'];
			$trunk=$_POST['trunk'];
			$record=$_POST['record'];
			$voip_type=$_POST['voip_type'];
			$answer=$_POST['answer'];
			$stream_quality=$_POST['stream_quality'];
			$sip_caller_ip=$_POST['sip_caller_ip'];
			
			$cong_no=rand(100000,999999);
			$conf_admin=rand(10000,99999);
			$conf_user=rand(10000,99999);
			$test=0;
			
			$db=file("config/meetme.conf");
			while ($test==0){
				foreach($db as $line){
					if (strstr($line,$cong_no)){
					$cong_no=rand(100000,999999);
					$test=-1;
					}
				}
				if ($test==-1){
				$test=0;
				}else{
				$test=1;
				}
			}
			if ($error!="ko"){
			
			$info=$cong_name."**".$cong_no."**".$conf_admin."**".$conf_user."**".$phone_no."**".$voip_type."**".$stream."**".$stream_type."**".$voip_pwd."**".$trunk."**".$record."**".$answer."**".$stream_quality."**".$sip_caller_ip."**\n";
			$info1="conf => ".$cong_no.",".$conf_user.",".$conf_admin."\n";
if ($stream_type=="mp3"){
$tmp_type="EAGI(".$web_server_root."kh-live/config/mp3stream-".$cong_name.".sh)";
}else{
$tmp_type="ICES(".$web_server_root."kh-live/config/asterisk-ices-".$cong_name.".xml)";
}
$info2=";".$cong_name."-start
[test-menu]
exten => ".$cong_no.",1,Playback(grg/".$cong_name.")
 same => n,Set(CURRENT_CONG=".$cong_name.")
 same => n,Set(CURRENT_CONF=".$cong_no.")
 same => n,Set(ADMIN_PIN=".$conf_admin.")
 same => n,Set(USER_PIN=".$conf_user.")
 same => n,Goto(grg-id,1)
exten => ices_".$cong_name.",1,Answer()
 same => n,Set(LISTENED_TO_RECORD=0)
 same => n,Wait(1)
 same => n,".$tmp_type."
 same => n,Hangup()
exten => meet_me_".$cong_name.",1,Answer()
 same => n,Set(LISTENED_TO_RECORD=0)
 same => n,Meetme(".$cong_no.",qlMx,".$conf_user.")
 same => n,Hangup()
exten => meet_me_".$cong_name."_admin,1,Answer()
 same => n,Set(LISTENED_TO_RECORD=0)
 same => n,Set(CURRENT_CONG=".$cong_name.")
 same => n,Set(CURRENT_CONF=".$cong_no.")
 same => n,Set(ADMIN_PIN=".$conf_admin.")
 same => n,Goto(grg-meetme,start,ADMIN)
 same => n,Hangup()
exten=> test_meeting_".$cong_name.",1,Answer()
 same => n,Set(CURRENT_CONG=".$cong_name.")
 same => n,Set(DB(Testing_0/".$cong_name.")=1)
 same => n(SOUND),Playback(grg/automatic_connect)
 same => n,Wait(10)
 same => n,Goto(SOUND)
 ;".$cong_name."-stop
 ";
			$file=fopen('./db/cong','a');
			if(fputs($file,$info)){
			fclose($file);
				$file1=fopen('./config/meetme.conf','a');
				if (fputs($file1,$info1)){
				fclose($file1);
					$file2=fopen('./config/extensions_custom.conf','a');
					if (fputs($file2,$info2)){
					fclose($file2);
					}else{
			echo '<div id="error_msg">'.$lng['error'].'2</div>';
			}
				}else{
			echo '<div id="error_msg">'.$lng['error'].'1</div>';
			}
			//we need to releoad the dialplan as we've made changes to it. We dont need to do anything about meetme.cong as it is reload everytime we start a meetme()
			exec($asterisk_bin.' -rx "dialplan reload"');
			if ($stream_type=="mp3"){
			$stream_path="/stream-".$cong_name;
			}else{
			$stream_path="/stream-".$cong_name.".ogg";
			}
			$db=file("db/streams");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$stream_path) $error_stream="ko";
			}
			if ($error_stream!="ko"){
			$info=$stream_path.'**'.$cong_name.'**'.$stream_type."** **\n"; //sanitize input - last field was for the stream friendly name which we dont really need. remove from other pages then clear.
			$file=fopen('./db/streams','a');
			if(fputs($file,$info)){
			fclose($file);
			$info2="<!--mount-".$cong_name."-->
<mount>
	<mount-name>".$stream_path."</mount-name>
	<username>source</username>
        <password>".$voip_pwd."</password>
<authentication type=\"url\">
	<option name=\"mount_add\" value=\"http://".$server_in."/kh-live/stream_start.php\"/>
        <option name=\"mount_remove\" value=\"http://".$server_in."/kh-live/stream_end.php\"/>
	<option name=\"listener_add\" value=\"http://".$server_in."/kh-live/listener_joined.php\"/>
        <option name=\"listener_remove\" value=\"http://".$server_in."/kh-live/listener_left.php\"/>
	<option name=\"auth_header\" value=\"icecast-auth-user: 1\"/>
</authentication>
</mount>
<!--lastmount-->
";
	//do not change the amount of lines added as it will break the delete function
	$db=file("config/icecast.xml");
			$file_content="";
	foreach($db as $line){
		if (strstr($line,"<!--lastmount-->")){
		$file_content.=$info2;
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/icecast.xml','w');
			if(fputs($file,$file_content)){
			fclose($file);
			// we need to restart icecast as it's config file changed but only if there is no meeting streaming at the time
			// we could restart even if ther is a meeting as it doesnt seem to crash the stream... it's just a reload not a restart
			//icecast needs to run as the same user as the webserver for it to work
			//we should still check that we dont edit the cong while there is a meeting in that same cong...
			$db=file("db/live_streams");
			if (count($db)==0){
			exec("kill -s HUP $(pidof ".$icecast_bin.")");
			}
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}

			
$info3="Channel: Local/ices_".$cong_name."@test-menu
MaxRetries: 0
WaitTime: 60
Context: test-menu
Extension: meet_me_".$cong_name."
Priority: 1
";
			$file=fopen('./config/stream_'.$cong_name.'.call','w');
			if(fputs($file,$info3)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'3</div>';
			}
if ($stream_type=="mp3"){
$bitrate=15+(3*$stream_quality);
$info4 = "<ezstream>
    <url>http://".$server_in.":".$port."/stream-".$cong_name."</url>
    <sourcepassword>".$voip_pwd."</sourcepassword>
    <format>MP3</format>
    <filename>stdin</filename>
    <stream_once>1</stream_once>
    <svrinfoname>My Stream</svrinfoname>
    <svrinfourl>http://".$server_out.":".$port."/stream-".$cong_name."</svrinfourl>
    <svrinfogenre>Live calls</svrinfogenre>
    <svrinfodescription>Stream from ".str_replace("_"," ", $cong_name)." Meeting</svrinfodescription>
    <svrinfobitrate>".$bitrate."</svrinfobitrate>
    <svrinfoquality>1</svrinfoquality>
    <svrinfochannels>1</svrinfochannels>
    <svrinfosamplerate>8000</svrinfosamplerate>
    <svrinfopublic>0</svrinfopublic>
</ezstream>";
$file=fopen('./config/asterisk-ices-'.$cong_name.'.xml','w');
			if(fputs($file,$info4)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'4</div>';
			}
$info5="#!/bin/sh\ncat /dev/fd/3 | ".$lame_bin." --preset cbr ".$bitrate." -r -m m -s 8.0 --bitwidth 16 - - | ".$ezstream_bin." -c ".$web_server_root."/kh-live/config/asterisk-ices-".$cong_name.".xml";
$file=fopen('./config/mp3stream-'.$cong_name.'.sh','w');
			if(fputs($file,$info5)){
			fclose($file);
			//the file needs to have exec rights to work as an agi script we might not need to give 5 to nobody
			chmod('./config/mp3stream-'.$cong_name.'.sh', 0755);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'5</div>';
			}
}else{			
$info4="<?xml version=\"1.0\"?>
<ices>
    <background>0</background>
    <logpath>/var/log/ices</logpath>
    <logfile>ices.log</logfile>
    <loglevel>2</loglevel>
    <consolelog>0</consolelog>
    <stream>
        <metadata>
            <name>".str_replace("_"," ", $cong_name)." Meeting </name>
            <genre>Live calls</genre>
            <description>Stream from ".str_replace("_"," ", $cong_name)." Meeting</description>
            <url>http://".$server_out.":".$port."/stream-".$cong_name.".ogg</url>
        </metadata>
        <input>
            <module>stdinpcm</module>
            <param name=\"rate\">8000</param>
            <param name=\"channels\">1</param>
            <param name=\"metadata\">0</param>
            <param name=\"metadatafilename\"> </param>
        </input>
        <instance>
            <hostname>".$server_in."</hostname>
            <port>".$port."</port>
            <password>".$voip_pwd."</password>
            <mount>/stream-".$cong_name.".ogg</mount>
            <yp>0</yp>
            <encode>  
                <quality>".$stream_quality."</quality>
                <samplerate>8000</samplerate>
                <channels>1</channels>
            </encode>
            <downmix>0</downmix>
        </instance>
    </stream>
</ices>
";
/* we must also add the cong's voip account in /config/iax_custom.conf
add what about sip??
[79179]
username=79179
deny=0.0.0.0/0.0.0.0
secret=test_1234
transfer=yes
context=from-internal
host=dynamic
type=friend
port=4569
qualify=yes
dial=IAX2/79179
permit=0.0.0.0/0.0.0.0
requirecalltoken=no
callerid=Lionel test iax <79179>

iax2 reload
we should also probably reload the dialplan and maybe also icecast
*/

			$file=fopen('./config/asterisk-ices-'.$cong_name.'.xml','w');
			if(fputs($file,$info4)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'4</div>';
			}
			
			}
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
include "sip-gen.php";
include "iax-gen.php";
include "alsa-gen.php";
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
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
<select name="voip_type">
<option value="none">none</option>
<option value="sip">SIP</option>
<option value="iax">IAX</option>
<option value="alsa">Alsa (direct input)</option>
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