<?PHP
include ('db/config.php');
exec ('wget -q -O - http://kh-live.co.za/ip.php?tmp='.rand(10000,100000).'|sed s/[^0-9.]//g', $resp_exec);
$current_ip=implode("",$resp_exec);
if (file_exists($temp_dir.'global_ip')){
$previous_ip=implode("",file($temp_dir.'global_ip'));
}else{
$previous_ip="";
}
if ($current_ip!=$previous_ip){
	$key=$master_key;
	$key2=$api_key;
	$string=time()."**update**".$server_out;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ok"){
	//update succesful
		if ($dec[0]==$current_ip){
	$file=fopen($temp_dir.'global_ip','w');
	fputs($file,$current_ip);
	fclose($file);
	}else{
	$file=fopen($temp_dir.'error_ip','w');
	fputs($file,$string."--".$response."--".$decrypted."--".$key2);
	fclose($file);
	}
	}elseif(@$dec[0]=="timeout"){
	$real_time=$dec[1];
		if (is_numeric($real_time)){
		//we need to update the server clock
		// it will return "date: cannot set date: Operation not permitted" if the user asterisk doesn't have the rights to do it.
		//asterisk ALL=NOPASSWD: /bin/date must be added to /etc/sudoers
		exec('sudo date +%s -s @'.$real_time, $date_exec);
		if (is_array($date_exec)){
			if (strstr(implode("", $date_exec), "Operation not permitted")){
		$info=time().'**error**set date permission denied'."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			}else{
			$info=time().'**info**auto set date succesful'."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			}
		}else{
		$info=time().'**error**set date error'."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
		}
		}
	}else{
	$file=fopen($temp_dir.'error_ip','w');
	fputs($file,$string."--".$response."--".$decrypted."--".$key2);
	fclose($file);
	}
}
?>