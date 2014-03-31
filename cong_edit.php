<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['cong_confirmed']!=""){
		//start cong del
			$cong_confirmed=urldecode($_POST['cong_confirmed']);//sanitize
			$db=file("db/cong");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$cong_confirmed){
		$cong_no=@$data[1];
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/cong','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			$db=file("config/meetme.conf");
			$file_content="";
	foreach($db as $line){
		if (strstr($line,$cong_no)){
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/meetme.conf','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			$db=file("db/streams");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[1]==$cong_confirmed){
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/streams','w');
			if(fputs($file,$file_content)){
			fclose($file);
			
			$db=file("config/icecast.xml");
			$file_content="";
			$line_to_skip=0;
	foreach($db as $line){
		if (strstr($line,'<!--mount-'.$cong_confirmed.'-->')){
		 $line_to_skip=12;
		}elseif($line_to_skip>>0){
		$line_to_skip--;
		}else{
		$file_content.=$line;
		}
		//fix this algo so it's not dependent on the amount of lines
	}
			$file=fopen('config/icecast.xml','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			$db=file("config/extensions_custom.conf");
			$file_content="";
			$skip=0;
	foreach($db as $line){
		if (strstr($line,';'.$cong_confirmed.'-start')){
		 $skip=1;
		 }elseif(strstr($line,';'.$cong_confirmed.'-stop')){
		 $skip=0;
		}elseif($skip==1){
		//do nothing
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/extensions_custom.conf','w');
			if(fputs($file,$file_content)){
			fclose($file);
			unlink('config/asterisk-ices-'.$cong_confirmed.'.xml');
			unlink('config/stream_'.$cong_confirmed.'.call');
			/*remove .sh file if the stream was mp3*/
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
		//end of delete cong
		//start add cong
		$error="";
		$error_stream="";
			$cong_name=$_POST['cong_name']; //check
			$cong_no=$_POST['cong_no'];
			$conf_admin=$_POST['conf_admin'];
			$conf_user=$_POST['conf_user'];
			$phone_no=$_POST['phone_no'];
			$voip_type=$_POST['voip_type'];
			$stream=$_POST['stream'];
			$stream_type=$_POST['stream_type'];
			$voip_pwd=$_POST['voip_pwd'];
			$trunk=$_POST['trunk'];
			$record=$_POST['record'];
			$voip_type=$_POST['voip_type'];
			$answer=$_POST['answer'];
			$stream_quality=$_POST['stream_quality'];
			
			//check that those ids are unique
			$db=file("db/cong");
				foreach($db as $line){
					$data=explode ("**",$line);
					if ($data[0]==$cong_name OR $data[4]==$phone_no) $error="ko";
				}
			
			$db=file("config/meetme.conf");
				foreach($db as $line){
					if (strstr($line,$cong_no) OR strstr($line,$conf_admin) OR strstr($line,$conf_user)) $error="ko";
				}
			
			if ($error!="ko"){
			
			$info=$cong_name."**".$cong_no."**".$conf_admin."**".$conf_user."**".$phone_no."**".$voip_type."**".$stream."**".$stream_type."**".$voip_pwd."**".$trunk."**".$record."**".$answer."**".$stream_quality."**\n";
			$info1="conf => ".$cong_no.",".$conf_user.",".$conf_admin."\n";
if ($stream_type=="mp3"){
$tmp_type="EAGI(/var/www/html/kh-live/config/mp3stream-".$cong_name.".sh)";
}else{
$tmp_type="ICES(/var/www/html/kh-live/config/asterisk-ices-".$cong_name.".xml)";
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
			exec('asterisk -rx "dialplan reload"');
			
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
	<option name=\"mount_add\" value=\"http://localhost/kh-live/stream_start.php\"/>
        <option name=\"mount_remove\" value=\"http://localhost/kh-live/stream_end.php\"/>
	<option name=\"listener_add\" value=\"http://localhost/kh-live/listener_joined.php\"/>
        <option name=\"listener_remove\" value=\"http://localhost/kh-live/listener_left.php\"/>
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
			exec("kill -s HUP $(pidof icecast)");
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
    <url>http://localhost:8000/stream-".$cong_name."</url>
    <sourcepassword>".$voip_pwd."</sourcepassword>
    <format>MP3</format>
    <filename>stdin</filename>
    <stream_once>1</stream_once>
    <svrinfoname>My Stream</svrinfoname>
    <svrinfourl>http://khlive.mooo.com:8000/stream-".$cong_name."</svrinfourl>
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
$info5="#!/bin/sh\ncat /dev/fd/3 | /usr/bin/lame --preset cbr ".$bitrate." -r -m m -s 8.0 --bitwidth 16 - - | ezstream -c /var/www/html/kh-live/config/asterisk-ices-".$cong_name.".xml";
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
            <url>http://khlive.mooo.com:8000/stream-".$cong_name.".ogg</url>
        </metadata>
        <input>
            <module>stdinpcm</module>
            <param name=\"rate\">8000</param>
            <param name=\"channels\">1</param>
            <param name=\"metadata\">0</param>
            <param name=\"metadatafilename\"> </param>
        </input>
        <instance>
            <hostname>localhost</hostname>
            <port>8000</port>
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
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
/* we must also add the cong's voip account in /config/iax_custom.conf or sip

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
*/

		//end cong add
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
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
<select name="voip_type">
<option value="none" <?PHP if ($voip_type=="none") echo 'selected=selected';?>>none</option>
<option value="sip" <?PHP if ($voip_type=="sip") echo 'selected=selected';?>>SIP</option>
<option value="iax" <?PHP if ($voip_type=="iax") echo 'selected=selected';?>>IAX</option>
</select><br /><br />
<b>Voip account number (Phone no)</b><br />
<input class="field_login" type="text" name="phone_no" value="<?PHP echo $phone_no;?>" />
<br /><br />
<b>Enable streaming</b><br />
<select name="stream">
<option value="yes" <?PHP if ($stream=="yes") echo 'selected=selected';?>>yes</option>
<option value="no" <?PHP if ($stream=="no") echo 'selected=selected';?>>no</option>
</select><br /><br />
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
<b>Congregation ID (for trunk auth)</b><br />
<input class="field_login" type="text" name="cong_no" value="<?PHP echo $cong_no;?>" />
<br /><br />
<b>Conf Admin PIN (for trunk auth)</b><br />
<input class="field_login" type="text" name="conf_admin" value="<?PHP echo $conf_admin;?>" />
<br /><br />
<b>Conf User PIN (for trunk auth)</b><br />
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