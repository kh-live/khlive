<?PHP
$time5='';
$time6='';
$time7='';
$start= time();
include ('db/config.php');
$time2= time()-$start;
exec ('wget -q -O - http://kh-live.co.za/ip.php|sed s/[^0-9.]//g', $resp_exec);
$time3= time()-$start;
$current_ip=implode("",$resp_exec);
if (file_exists($temp_dir.'global_ip')){
$previous_ip=implode("",file($temp_dir.'global_ip'));
}else{
$previous_ip="";
}
$time4= time()-$start;
if ($current_ip!=$previous_ip){
	$key=$master_key;
	$key2=$api_key;
	$string=time()."**update**".$server_out;
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$time5= time()-$start;
	$response=file_get_contents('http://kh-live.co.za/api.php?q='.urlencode($encrypted));
	$time6= time()-$start;
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$time7= time()-$start;
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
	echo $decrypted;
	}
}
$time8= time()-$start;
echo 'Include : '.$time2."s \n";
echo 'Chekip : '.$time3."s \n";
echo 'Read file : '.$time4."s \n";
echo 'Encrypt : '.$time5."s \n";
echo 'Get api : '.$time6."s \n";
echo 'Decrypt : '.$time7."s \n";
echo 'End : '.$time8."s \n";
?>