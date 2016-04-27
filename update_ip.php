<?PHP
include ('db/config.php');
exec ('wget -q -O - http://kh-live.co.za/ip.php|sed s/[^0-9.]//g', $resp_exec);
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
	echo "current_ip :".$current_ip."\n";
	echo "received_ip :".$dec[0]."\n";
	echo "error wrong ip";
	}
	}else{
	echo "error synchronising ip";
	}
}
?>