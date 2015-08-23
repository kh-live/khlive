<?PHP
function kh_user_add($user,$password,$name,$congregation,$rights,$pin,$type,$last_login,$info,$encode="1",$api="0"){
$error="ok";
global $server_beta;
global $lng;
global $asterisk_bin;
global $master_key;
global $api_key;
global $auto_khlive;
		if ($rights!="0" AND $congregation!="0" AND $user!="" AND $password!="" AND strlen($password)>=8 AND $name!="" AND $pin>=9999 AND $pin<=100000){
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$user) $error="ko";
			if ($data[5]==$pin) $error="ko";
			}
			//remote check
			if ($server_beta!="master" AND $api=="0" AND $auto_khlive=="yes"){
			$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_check**".$user.'###'.$congregation;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	//if upstream server fails to answer should we still add the user locally?
	if (@$dec[1]=="ko") $error="ko";
			}
			
			if ($error!="ko"){
			if ($encode=="1"){
			$salt=hash("sha512",rand());
			$pwd_hashed=hash("sha512",$salt.$password);
			$password=$salt.'--'.$pwd_hashed;
			}
			$info=$user.'**'.$password.'**'.$name.'**'.$congregation.'**'.$rights.'**'.$pin.'**'.$type."**".$last_login."**".$info."**\n"; //sanitize input
			$file=fopen('./db/users','a');
			if(fputs($file,$info)){
			fclose($file);
			}else{
			$error='ko';
			}

if ($server_beta=="false"){
			//add account for voip only on production server (not on master server)
include "sip-gen.php";
include "iax-gen.php";
exec($asterisk_bin.' -rx "database put '.$congregation.' '.$pin.' '.$user.'"');
}
if ($api=="0"){
if ($server_beta!="master"){
//we need to sync with master server only if we are not called by api (otherwise it loops)
if ($auto_khlive=="yes"){
$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_add**".$user.'###'.$password.'###'.$name.'###'.$congregation.'###'.$rights.'###'.$pin.'###'.$type."###".$info."**";
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ko") $error="ko";
}	
}else{
//we need to sync with slave server only with the function isn't called from api!
//which server to contact?
$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$congregation)){
	$api_key=$data[2];
	$slave_url=$data[1];
	}
	}
if ($slave_url!=""){
$key=$api_key;
	$key2=$api_key;
	$string=time()."**user_add**".$user.'###'.$password.'###'.$name.'###'.$congregation.'###'.$rights.'###'.$pin.'###'.$type."###".$info."**";
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://'.$slave_url.'/kh-live/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	//what happens if the server is not reachable? the error doesnt become ko so the function still returns ok... is it what we want?
	if (@$dec[1]=="ko") $error="ko";
	}
}
}
			if ($error=='ok'){
			return 'ok';
			}else{
			return '<div id="error_msg">'.$lng['error'].'</div>';
			}
			}else{
			return '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
		}else{
		return '<div id="error_msg">'.$lng['fill_incorrect'].'...</div>';
		}
}

function kh_user_del($user_confirmed,$pin,$api="0"){
//when this fuction is called from api the session is not set
global $server_beta;
global $lng;
global $asterisk_bin;
global $master_key;
global $api_key;
global $auto_khlive;
$skip=0;
$error='ok';
			$db=file("db/users");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		
		if ($data[0]==$user_confirmed){
			if ($data[3]==@$_SESSION['cong'] OR @$_SESSION['type']=='root' OR $api=="1"){
			$congregation=$data[3];
			}else{
			//this an attempt at deleting a user from another cong - log
			$file_content.=$line;
			$skip=1;
			}
		}else{
		$file_content.=$line;
		}
		
	}
	if ($skip==0){
			$file=fopen('./db/users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			$error='ko';
			}
if ($server_beta=="false"){
//remove voip account if we are on production server
exec($asterisk_bin.' -rx "database del '.$congregation.' '.$pin.'"');
include "sip-gen.php";
include "iax-gen.php";
}
if ($api=="0"){
if ($server_beta!="master"){
//we need to sync with master server only if we are not called by api (otherwise it loops)
if ($auto_khlive=="yes"){
$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_del**".$user_confirmed.'###'.$congregation.'###'.$pin;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ko") $error="ko";
	}
}else{
//we need to sync with slave server only with the function isn't called from api!
//which server to contact?
$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$congregation)){
	$api_key=$data[2];
	$slave_url=$data[1];
	}
	}
if ($slave_url!=""){
$key=$api_key;
	$key2=$api_key;
	$string=time()."**user_del**".$user_confirmed.'###'.$congregation.'###'.$pin;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://'.$slave_url.'/kh-live/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	//what happens if the server is not reachable? the error doesnt become ko so the function still returns ok... is it what we want?
	if (@$dec[1]=="ko") $error="ko";
	}
}
}
			if ($error=='ok'){
			return 'ok';
			}else{
			return '<div id="error_msg">'.$lng['error'].'</div>';
			}
		}else{
		//this an attempt at deleting a user from another cong - log
		return '<div id="error_msg">'.$lng['error'].'</div>';
		}
}
function cong_add($cong_name, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $voip_type, $answer, $stream_quality, $sip_caller_ip, $cong_no="", $conf_admin="", $conf_user=""){
if ($cong_no=="") $cong_no=rand(100000,999999);
if ($conf_admin=="") $conf_admin=rand(10000,99999);
if ($conf_user=="") $conf_user=rand(10000,99999);
global $lng;
global $server_in;
global $web_server_root;
global $asterisk_bin;
global $icecast_bin;
global $lame_bin;
global $ezstream_bin;
global $port;
global $server_audio;
global $alsa_in;
global $alsa_out;
if ($stream_server=="") $stream_server=$server_in;

		$db=file("db/cong");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$cong_name OR $data[4]==$phone_no)
			return '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
			
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
			
	$info=$cong_name."**".$cong_no."**".$conf_admin."**".$conf_user."**".$phone_no."**".$voip_type."**".$stream."**".$stream_type."**".$voip_pwd."**".$trunk."**".$record."**".$answer."**".$stream_quality."**".$sip_caller_ip."**".$stream_server."**\n";
		$file=fopen('./db/cong','a');
			if(fputs($file,$info)){
			fclose($file);
			}else{
			return 'error saving /db/cong';
			}
			
				if ($stream_type=="mp3"){
			$stream_path="/stream-".$cong_name;
		}else{
			$stream_path="/stream-".$cong_name.".ogg";
		}
		
	$db=file("db/streams");
		foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$stream_path){
			return 'error stream name exists';
			}
		}

	$info=$stream_path.'**'.$cong_name.'**'.$stream_type."** **\n"; //sanitize input - last field was for the stream friendly name which we dont really need. remove from other pages then clear.
			
			$file=fopen('./db/streams','a');
			if(fputs($file,$info)){
			fclose($file);
			}else{
			return 'error saving db/streams';
			}
			
	$info1="conf => ".$cong_no.",".$conf_user.",".$conf_admin."\n";
		$file1=fopen('./config/meetme.conf','a');
			if (fputs($file1,$info1)){
				fclose($file1);
			}else{
				return 'error saving /config/meetme.conf';
			}
			
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
		$file2=fopen('./config/extensions_custom.conf','a');
			if (fputs($file2,$info2)){
					fclose($file2);
			}else{
					return 'error saving /config/extensions_custom.conf';
			}
				
			//we need to releoad the dialplan as we've made changes to it. We dont need to do anything about meetme.cong as it is reload everytime we start a meetme()
			exec($asterisk_bin.' -rx "dialplan reload"');
			

// we might need to change the stream to url in case we stream on another server. but then the meeting wont start on local...
//no need to change as the congregation has to be created on remote server too.
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
<!--mount-end-".$cong_name."-->
<!--lastmount-->
";

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
			}else{
			return 'error saving /config/icecast.xml';
			}

			// we need to restart icecast as it's config file changed
			//icecast needs to run as the same user as the webserver for it to work
			//we should still check that we dont edit the cong while there is a meeting in that same cong...

			exec("kill -s HUP $(pidof ".$icecast_bin.")");
			
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
			return 'error saving /config/stream-cong.call';
			}
			

if ($stream_type=="mp3"){
//we stream in mp3
	$bitrate=15+(3*$stream_quality);
	$info4 = "<ezstream>
    <url>http://".$stream_server.":".$port."/stream-".$cong_name."</url>
    <sourcepassword>".$voip_pwd."</sourcepassword>
    <format>MP3</format>
    <filename>stdin</filename>
    <stream_once>1</stream_once>
    <svrinfoname>My Stream</svrinfoname>
    <svrinfourl>http://".$server_in.":".$port."/stream-".$cong_name."</svrinfourl>
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
			return 'error saving asterisk-ices.xml mp3';
			}
			
	$info5="#!/bin/sh\ncat /dev/fd/3 | ".$lame_bin." --preset cbr ".$bitrate." -r -m m -s 8.0 --bitwidth 16 - - | ".$ezstream_bin." -c ".$web_server_root."/kh-live/config/asterisk-ices-".$cong_name.".xml";

	$file=fopen('./config/mp3stream-'.$cong_name.'.sh','w');
			if(fputs($file,$info5)){
			fclose($file);
			}else{
			return 'error saving mp3-stream.sh';
			}
			//the file needs to have exec rights to work as an agi script we might not need to give 5 to nobody
			chmod('./config/mp3stream-'.$cong_name.'.sh', 0755);
}else{
//we stream in ogg
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
            <url>http://".$server_in.":".$port."/stream-".$cong_name.".ogg</url>
        </metadata>
        <input>
            <module>stdinpcm</module>
            <param name=\"rate\">8000</param>
            <param name=\"channels\">1</param>
            <param name=\"metadata\">0</param>
            <param name=\"metadatafilename\"> </param>
        </input>
        <instance>
            <hostname>".$stream_server."</hostname>
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
	$file=fopen('./config/asterisk-ices-'.$cong_name.'.xml','w');
		if(fputs($file,$info4)){
			fclose($file);
		}else{
			return 'error saving astersk-ices.xml ogg';
		}
			
}

include "sip-gen.php";
include "iax-gen.php";
include "alsa-gen.php";

return 'ok';
} 

function cong_del($cong_confirmed,$edit){
global $asterisk_bin;
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
	if (strlen($file_content)==0){
		unlink('db/cong');
		touch('db/cong');
		}else{
			$file=fopen('./db/cong','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			return 'error saving /db/cong';
			}
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
	if (strlen($file_content)==0){
		unlink('db/streams');
		touch('db/streams');
		}else{
			$file=fopen('./db/streams','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			return 'error saving /db/streams';
			}
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
			}else{
			return 'error saving meetme.conf';
			}
			
$db=file("config/icecast.xml");
			$file_content="";
			$skip='';
	foreach($db as $line){
		if (strstr($line,'<!--mount-'.$cong_confirmed.'-->')){
		 $skip='ok';
		}elseif(strstr($line,'<!--mount-end-'.$cong_confirmed.'-->') ){
		$skip='';
		}elseif(strstr($line, '<!--lastmount-->')){
		$skip='';
		$file_content.="<!--lastmount-->\n";
		}elseif ($skip==''){
		$file_content.=$line;
		}
	}
			$file=fopen('config/icecast.xml','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			return 'error saving icecast.xml';
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
			}else{
			return 'error saving extensions_custom.conf';
			}
			
unlink('config/asterisk-ices-'.$cong_confirmed.'.xml');
unlink('config/stream_'.$cong_confirmed.'.call');
include "sip-gen.php";
include "iax-gen.php";
//should we reload icecast and asterisk and should we regen alsa-gen?
//we must still remove the old recordings if we don't do an edit
//we must also remove the logs if it's not an edit
//we must check that users are not left orphan if it's not an edit
return 'ok';
}
?>