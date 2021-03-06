<?PHP
function kh_encrypt($string,$api_key){
	$ivlen = openssl_cipher_iv_length($cipher="aes-256-cbc");
	$iv = openssl_random_pseudo_bytes($ivlen);
	$key=hash('sha512', $api_key, true);
	$ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
	$hmac = hash_hmac('sha512', $ciphertext_raw, $key, $as_binary=true);
	$encrypted = base64_encode( $iv.$hmac.$ciphertext_raw );
	return $encrypted;
}
function kh_decrypt($q,$api_key){
$c = base64_decode($q);
$ivlen = openssl_cipher_iv_length($cipher="aes-256-cbc");
$iv = substr($c, 0, $ivlen);
$hmac = substr($c, $ivlen, $sha2len=64);
$ciphertext_raw = substr($c, $ivlen+$sha2len);
$key=hash('sha512', $api_key, true);
$decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
$calcmac = hash_hmac('sha512', $ciphertext_raw, $key, $as_binary=true);
if (hash_equals($hmac, $calcmac)){
return $decrypted;
}else{
return false;
}
}

function kh_user_add($user,$password,$name,$congregation,$rights,$pin,$type,$last_login,$info,$encode="1",$api="0"){
$error="ok";
global $server_beta;
global $lng;
global $asterisk_bin;
global $master_key;
global $api_key;
global $auto_khlive;
global $https;
global $ttl_back;
		if ($rights!="0" AND $congregation!="0" AND $user!="" AND $password!="" AND strlen($password)>=8 AND $name!="" AND $pin>=9999 AND $pin<=100000){
			if (file_exists("db/users")){
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$user) $error="ko";
			if ($data[5]==$pin) $error="ko";
			}
			}
			//remote check
			if ($server_beta!="master" AND $api=="0" AND $auto_khlive=="yes"){
			$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_check**".$user.'###'.$congregation;
	$encrypted=kh_encrypt($string,$key);
	$response=kh_fgetc_timeout($https.'://kh-live.co.za/api.php?q='.urlencode($encrypted), $ttl_back);
	$decrypted = kh_decrypt($response,$key2);
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
			$info0=$user.'**'.$password.'**'.$name.'**'.$congregation.'**'.$rights.'**'.$pin.'**'.$type."**".$last_login."**".$info."**\n"; //sanitize input
			$file=fopen('./db/users','a');
			if(fputs($file,$info0)){
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
	$encrypted=kh_encrypt($string,$key);
	$response=kh_fgetc_timeout($https.'://kh-live.co.za/api.php?q='.urlencode($encrypted), $ttl_back);
	$decrypted = kh_decrypt($response,$key2);
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
	$slave_url=$data[0];
	$q_proto='http://';
	$q_port=':80';
	if (@$data[6]!='' AND is_numeric(@$data[6])) $q_port=':'.$data[6];
	if (@$data[5]=='auto' OR @$data[5]=='force'){
		$q_proto='https://';
		$q_port=':443';
		if (@$data[7]!='' AND is_numeric(@$data[7])) $q_port=':'.$data[7];
	}
	}
	}
if ($slave_url!=""){
$key=$api_key;
	$key2=$api_key;
	$string=time()."**user_add**".$user.'###'.$password.'###'.$name.'###'.$congregation.'###'.$rights.'###'.$pin.'###'.$type."###".$info."**";
	$encrypted=kh_encrypt($string,$key);
	$response=kh_fgetc_timeout($q_proto.$slave_url.$q_port.'/kh-live/api.php?q='.urlencode($encrypted), $ttl_back);
	$decrypted = kh_decrypt($response,$key2);
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
global $https;
global $ttl_back;
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
	$encrypted=kh_encrypt($string,$key);
	$response=kh_fgetc_timeout($https.'://kh-live.co.za/api.php?q='.urlencode($encrypted), $ttl_back);
	$decrypted = kh_decrypt($response,$key2);
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
	$slave_url=$data[0];
				$q_proto='http://';
				$q_port=':80';
				if ($data[6]!='' AND is_numeric($data[6])) $q_port=':'.$data[6];
				if ($data[5]=='auto' OR $data[5]=='force'){
					$q_proto='https://';
					$q_port=':443';
					if ($data[7]!='' AND is_numeric($data[7])) $q_port=':'.$data[7];
				}
	}
	}
if ($slave_url!=""){
$key=$api_key;
	$key2=$api_key;
	$string=time()."**user_del**".$user_confirmed.'###'.$congregation.'###'.$pin;
	$encrypted=kh_encrypt($string,$key);
	$response=kh_fgetc_timeout($q_proto.$slave_url.$q_port.'/kh-live/api.php?q='.urlencode($encrypted), $ttl_back);
	$decrypted = kh_decrypt($response,$key2);
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
function cong_add($cong_name, $cong_lang, $phone_no, $voip_type, $stream, $stream_server, $stream_type, $voip_pwd, $trunk, $record, $answer, $stream_quality, $sip_caller_ip, $cong_no="", $conf_admin="", $conf_user="", $jitsi_cong_address=""){
if ($cong_no=="") $cong_no=rand(100000,999999);
if ($conf_admin=="") $conf_admin=rand(10000,99999);
if ($conf_user=="") $conf_user=rand(10000,99999);
global $lng;
global $server_in;
global $server_out;
global $web_server_root;
global $asterisk_bin;
global $icecast_bin;
global $lame_bin;
global $ezstream_bin;
global $port;
global $server_audio;
global $alsa_in;
global $alsa_out;
global $sound_quality;
if ($stream_server=="") $stream_server=$server_in;
		if (strstr($cong_name, ' ')){
		return 'error space not allowed in cong name';
		}
		if (file_exists("db/cong")){
		$db=file("db/cong");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$cong_name OR $data[4]==$phone_no)
			return '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
		}	
		$test=0;
		if (file_exists("config/meetme.conf")){	
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
		}
			
	$info=$cong_name."**".$cong_no."**".$conf_admin."**".$conf_user."**".$phone_no."**".$voip_type."**".$stream."**".$stream_type."**".$voip_pwd."**".$trunk."**".$record."**".$answer."**".$stream_quality."**".$sip_caller_ip."**".$stream_server."**".$cong_lang."**".$jitsi_cong_address."**\n";
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
	if (file_exists("db/streams")){	
	$db=file("db/streams");
		foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$stream_path){
			return 'error stream name exists';
			}
		}
	}
	if ($stream_type!='both'){
	$info=$stream_path.'**'.$cong_name.'**'.$stream_type."** **\n"; //sanitize input - last field was for the stream friendly name which we dont really need. remove from other pages then clear.
		}else{
	$info=$stream_path.'**'.$cong_name."**ogg** **\n/stream-".$cong_name.'**'.$cong_name."**mp3** **\n"; //sanitize input - last field was for the stream friendly name which we dont really need. remove from other pages then clear.
}
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
//do we need to record the stream (dump-file)? Only if we use edcast to stream.
$dump_file="";
if($record=='yes' AND $voip_type=='none'){
	if ($stream_type=="ogg"){
	$dump_file='
	<dump-file>'.$web_server_root.'kh-live/records/'.$cong_name.'-%Y%m%d_%H%M%S.ogg</dump-file>';
	}else{
	$dump_file='
	<dump-file>'.$web_server_root.'kh-live/records/'.$cong_name.'-%Y%m%d_%H%M%S.mp3</dump-file>';
	}
}
if ($stream=='yes'){
if ($stream_type=="both"){
$info2="<!--mount-".$cong_name."-->
<mount>
	<mount-name>/stream-".$cong_name."</mount-name>
	<username>source</username>
        <password>".$voip_pwd."</password>".$dump_file."
<authentication type=\"url\">
	<option name=\"mount_add\" value=\"http://".$server_in."/kh-live/stream_start.php\"/>
        <option name=\"mount_remove\" value=\"http://".$server_in."/kh-live/stream_end.php\"/>
	<option name=\"listener_add\" value=\"http://".$server_in."/kh-live/listener_joined.php\"/>
        <option name=\"listener_remove\" value=\"http://".$server_in."/kh-live/listener_left.php\"/>
	<option name=\"auth_header\" value=\"icecast-auth-user: 1\"/>
</authentication>
</mount>
<!--mount-end-".$cong_name."-->
<!--mount-".$cong_name."-->
<mount>
	<mount-name>/stream-".$cong_name.".ogg</mount-name>
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
}else{
// we might need to change the stream to url in case we stream on another server. but then the meeting wont start on local...
//no need to change as the congregation has to be created on remote server too.
$info2="<!--mount-".$cong_name."-->
<mount>
	<mount-name>".$stream_path."</mount-name>
	<username>source</username>
        <password>".$voip_pwd."</password>".$dump_file."
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
}
	if (file_exists("config/icecast.xml")){	
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
		}
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
			

if ($stream_type=="mp3" OR $stream_type=="both"){
//we stream in mp3
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
		$bitrate= $bitrates[$sound_quality.'-'.$stream_quality];
	$info4 = "<ezstream>
    <url>http://".$stream_server.":".$port."/stream-".$cong_name."</url>
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
    <svrinfosamplerate>".$sound_quality."</svrinfosamplerate>
    <svrinfopublic>0</svrinfopublic>
</ezstream>";

	$file=fopen('./config/asterisk-ezstream-'.$cong_name.'.xml','w');
			if(fputs($file,$info4)){
			fclose($file);
			}else{
			return 'error saving asterisk-ezstream.xml mp3';
			}
			
	$info5="#!/bin/sh\ncat /dev/fd/3 | ".$lame_bin." --preset cbr ".$bitrate." -r -m m -s 8.0 --bitwidth 16 - - | ".$ezstream_bin." -c ".$web_server_root."/kh-live/config/asterisk-ezstream-".$cong_name.".xml";

	$file=fopen('./config/mp3stream-'.$cong_name.'.sh','w');
			if(fputs($file,$info5)){
			fclose($file);
			}else{
			return 'error saving mp3-stream.sh';
			}
			//the file needs to have exec rights to work as an agi script we might not need to give 5 to nobody
			chmod('./config/mp3stream-'.$cong_name.'.sh', 0755);
}
if ($stream_type=="ogg" OR $stream_type=="both"){
//we stream in ogg
$info4="<?xml version=\"1.0\"?>
<ices>
    <background>0</background>
    <logpath>/var/log/icecast2</logpath>
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
            <param name=\"rate\">".$sound_quality."</param>
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
                <samplerate>".$sound_quality."</samplerate>
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
global $server_beta;

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
if ($server_beta=="false"){			
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
if (file_exists('config/asterisk-ices-'.$cong_confirmed.'.xml')) unlink('config/asterisk-ices-'.$cong_confirmed.'.xml');
if (file_exists('config/asterisk-ezstream-'.$cong_confirmed.'.xml')) unlink('config/asterisk-ezstream-'.$cong_confirmed.'.xml');
if (file_exists('config/stream_'.$cong_confirmed.'.call')) unlink('config/stream_'.$cong_confirmed.'.call');
include "sip-gen.php";
include "iax-gen.php";
//should we reload icecast and asterisk and should we regen alsa-gen?
//we must still remove the old recordings if we don't do an edit
//we must also remove the logs if it's not an edit
//we must check that users are not left orphan if it's not an edit
}
return 'ok';
}


function sched_add($congregation, $day, $time_start, $time_stop, $enable, $timing){
$file_content=$congregation.'**'.$day.'**'.$time_start.'**'.$time_stop.'**'.$enable.'**'.$timing."**\n";
$file=fopen('db/sched','a');
			if(fputs($file,$file_content)){
			fclose($file);
			return 'ok';
			}else{
			return 'error saving db/sched while add';
			}
}
function sched_del($id){
$db=file("db/sched");
if ($db!=''){
			$file_content="";
			$i=0;
	foreach($db as $line){
		if ($i!=$id){
		$file_content.=$line;
		}
		$i++;
	}
			$file=fopen('db/sched','w');
			if(fputs($file,$file_content)){
			fclose($file);
			return 'ok';
			}else{
			//when we delete the last line in the file, fputs will give an error but it will still work....
			if ($file_content==''){
			return 'ok';
			}else{
			return 'error saving db/sched while delete';
			}
			}
}
}
function info_add($congregation, $infos, $link, $enable){
$file_content=$congregation.'**'.$infos.'**'.$link.'**'.$enable."**\n";
$file=fopen('db/infos','a');
			if(fputs($file,$file_content)){
			fclose($file);
			return 'ok';
			}else{
			return 'error saving db/infos while add';
			}
}
function info_del($id){
$db=file("db/infos");
if ($db!=''){
			$file_content="";
			$i=0;
	foreach($db as $line){
		if ($i!=$id){
		$file_content.=$line;
		}
		$i++;
	}
			$file=fopen('db/infos','w');
			if(fputs($file,$file_content)){
			fclose($file);
			return 'ok';
			}else{
			//when we delete the last line in the file, fputs will give an error but it will still work....
			if ($file_content==''){
			return 'ok';
			}else{
			return 'error saving db/infos while delete';
			}
			}
}
}
function timing_add($name, $timings){
$file_content=$name.'**'.serialize($timings)."**\n";
$file=fopen('db/timings','a');
			if(fputs($file,$file_content)){
			fclose($file);
			return 'ok';
			}else{
			return 'error saving db/timings while add';
			}
}
function timing_del($id){
$db=file("db/timings");
if ($db!=''){
			$file_content="";
			$i=0;
	foreach($db as $line){
		if ($i!=$id){
		$file_content.=$line;
		}
		$i++;
	}
			$file=fopen('db/timings','w');
			if(fputs($file,$file_content)){
			fclose($file);
			return 'ok';
			}else{
			//when we delete the last line in the file, fputs will give an error but it will still work....
			if ($file_content==''){
			return 'ok';
			}else{
			return 'error saving db/timings while delete';
			}
			}
}
}
function kh_fgetc_timeout($url,$timeout=10){
$ch=curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

$result=curl_exec($ch);
curl_close($ch);
return $result;
}
?>
