<?PHP
$string="";
include './db/config.php';
include './functions.php';
if (isset($_GET['q'])){
//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($api_key), base64_decode($_GET['q']), MCRYPT_MODE_CBC, md5(md5($api_key))), "\0");
$decrypted=kh_decrypt($_GET['q'], $api_key);
if ($decrypted!= false){
$query=explode("**", $decrypted);
	if ($query[0]+60>=time() AND time()>=$query[0]-60){
		if ($query[1]=="status"){
		exec("uname -m",$uname);
		$string=$version.'-'.@$enable_ssl."-".implode('',$uname)."-".phpversion()."@@@ok";
		}elseif($query[1]=="fetch_users"){
		$db=file('db/users');
			foreach($db as $line){
			$string.=$line;
			}
			$string.="@@@ok";
		}elseif($query[1]=="fetch_congs"){
		$db=file('db/cong');
			foreach($db as $line){
			$string.=$line;
			}
			$string.="@@@ok";
		}elseif ($query[1]=="update" AND $server_beta=="master"){
		$server_name=$query[2];
		$new_ip=$_SERVER['REMOTE_ADDR'];
		
		$content="";
		$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$server_name){
		$api_key=$data[2];
		$ip_addr='';
		if ($data[4]!='' AND $data[4]!="\n") $ip_addr=$data[4];
		if (@data[5]=="\n") $data[5]='';
		$content.=$server_name."**".$new_ip."**".$api_key."**".$data[3]."**".$ip_addr."**".@$data[5]."**".@$data[6]."**".@$data[7]."**\n";
	}else{
		$content.=$line;
	}
}
		if ($content!=''){
		$file=fopen("./db/servers",'w');
	if (fputs($file,$content)){
	fclose($file);
	
	//we still need to update cpanel dns
	$tmp=explode(".", $server_name);
	$hostname=str_replace("_","",$tmp[0]);
	if (file_exists('./dyndns.php')){
	include "dyndns.php";
	}
		$string=$new_ip."@@@ok";
		}
		//we log that the ip has been updated
		$info=time().'**info**ip updated**'.$server_name.'@'.$new_ip."--".$string."--".$api_key."**\n";
		}else{
		$string=$new_ip."@@@ko";
		$info=time().'**error**ip update colision**'.$server_name.'@'.$new_ip."**\n";
		}
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
		}elseif ($query[1]=="start" AND $server_beta=="master"){
		//we must make sure query2 is really a cong
		$cong=$query[2];
		$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$cong)){
	$api_key=$data[2];
	}
	}
		$file=fopen($temp_dir.'meeting_'.$cong,'w');
	if (fputs($file,"live")){
	fclose($file);
		$string=time()."@@@ok";
		//we log that the ip has been updated
		$info=time().'**info**stream started**'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}else{
	$string=time()."@@@ko";
	}
		}elseif ($query[1]=="stop" AND $server_beta=="master"){
		//we must make sure query2 is really a cong
		$cong=$query[2];
		$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$cong)){
	$api_key=$data[2];
	}
	}
		$file=fopen($temp_dir.'meeting_'.$cong,'w');
	if (fputs($file,"down")){
	fclose($file);
		$string=time()."@@@ok";
		//we log that the ip has been updated
		$info=time().'**info**stream stopped**'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}else{
	$string=time()."@@@ko";
	}
		}elseif ($query[1]=="user_check" AND $server_beta=="master"){
		//we must make sure query2 is really a cong
		$error="0";
		$param=explode('###',$query[2]);
		$cong=$param[1];
		$user_check=$param[0];
		
		$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$cong)){
	$api_key=$data[2];
	}
	}
	$db=file("./db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$user_check){
	$error="1";
	}
	}
	if ($error=="0"){
		$string=time()."@@@ok";
		//we log that the ip has been updated
		$info=time().'**info**user check successful**'.$user_check.'@'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}else{
	$string=time()."@@@ko";
	$info=time().'**info**user check fail**'.$user_check.'@'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
		}
	}elseif ($query[1]=="user_del" ){
		//we must make sure query2 is really a cong
		$error="0";
		$param=explode('###',$query[2]);
		$pin=$param[2];
		$cong=$param[1];
		$user_del=$param[0];
		
		if ($server_beta=="master"){
		$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$cong)){
	$api_key=$data[2];
	}
	}
	}
	//we tell the function not to loop back
	$api="1";
	$deleting=kh_user_del($user_del,$pin,$api);
	if ($deleting!='ok') $error="1";

	if ($error=="0"){
		$string=time()."@@@ok";
		//we log that the ip has been updated
		$info=time().'**info**user delete successful**'.$user_del.'@'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}else{
	$string=time()."@@@ko";
	$info=time().'**info**user del fail**'.$user_del.'@'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
		}
		}elseif ($query[1]=="user_add" ){
		//we must make sure query2 is really a cong
		$error="0";
		$param=explode('###',$query[2]);
		$user=$param[0];
		$password=$param[1];
		$name=$param[2];
		$cong=$param[3];
		$rights=$param[4];
		$pin=$param[5];
		$type=$param[6];
		$info=$param[7];
		if ($server_beta=="master"){
		$db=file("./db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$cong)){
	$api_key=$data[2];
	}
	}
	}
	//we tell the function not to loop back
	$api="1";
	$encode="0";
	$last_login=" ";
	$adding=kh_user_add($user,$password,$name,$cong,$rights,$pin,$type,$last_login,$info,$encode,$api);
	if ($adding!='ok') $error="1";

	if ($error=="0"){
		$string=time()."@@@ok";
		//we log that the ip has been updated
		$info=time().'**info**user added successful**'.$user.'@'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}else{
	$string=time()."@@@ko";
	$info=time().'**info**user add fail**'.$user.'@'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
		}
		}
	if ($string==""){
	$string="nothing@@@ko";
		}
	}else{
	//api_key is undefined when we timeout on master side
	if ($server_beta=="master"){
	$db=file("db/servers");
		foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$query[2]){
	$api_key=$data[2];
	}
	}
	}
	$string="timeout@@@".time();
	}
	$info=time().'**info**API call**Query : '.$decrypted.'**Response : '.$string."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
		$encrypted=kh_encrypt($string, $api_key);
		echo $encrypted;
		exit;
	}else{
	//tere was an error during decryption - the data has been tampered with
		$info=time().'**error**API hash not equal**Query : '.$_GET['q'].'**result of decryption : '.$decrypted."**\n";
		$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
		}
	}
}elseif (isset($_GET['check'])){
	$cong=$_GET['check'];
	if (is_file($temp_dir.'meeting_'.$cong)){
	if ($test=file_get_contents($temp_dir.'meeting_'.$cong)){
	if (strstr($test,"down")){
	echo "down";
	}else{
	echo "live";
	}
	}else{
	echo "error";
	}
	}else{
	echo "down";
	}
}
?>