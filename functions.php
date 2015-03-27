<?PHP
function kh_user_add($user,$password,$name,$congregation,$rights,$pin,$type,$last_login,$info,$encode="1",$api="0"){
$error="";
global $server_beta;
global $lng;
global $asterisk_bin;
global $master_key;
global $api_key;
		if ($rights!="0" AND $congregation!="0" AND $user!="" AND $password!="" AND strlen($password)>=8 AND $name!="" AND $pin>=9999 AND $pin<=100000){
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$user) $error="ko";
			if ($data[5]==$pin) $error="ko";
			}
			//remote check
			if ($server_beta!="master"){
			$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_check**".$user.'###'.$congregation;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
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

$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_add**".$user.'###'.$password.'###'.$name.'###'.$congregation.'###'.$rights.'###'.$pin.'###'.$type."###".$info."**";
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ko") $error="ko";
	
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

$key=$master_key;
	$key2=$api_key;
	$string=time()."**user_del**".$user_confirmed.'###'.$congregation.'###'.$pin;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ko") $error="ko";
	
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
?>