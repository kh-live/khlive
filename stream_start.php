<?PHP
include './db/config.php';
include './functions.php';
if (isset($cong)) unset($cong);
 if (isset($_POST['action'])){
	if ($_POST['action']=="mount_add"){
	$mount=$_POST['mount'];
	$server=$_POST['server'];
	$port=$_POST['port'];
	
	$db=file("db/streams");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$mount) $cong=$data[1];
	}
	
	if (isset($cong)){
	$info=time().'**info**new mount**'.$mount.'@'.$server.':'.$port.'**'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	$key=$master_key;
	$key2=$api_key;
	$string=time()."**start**".$cong;
	//$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$encrypted=kh_encrypt($string,$key);
	$response=kh_fgetc_timeout($https.'://kh-live.co.za/api.php?q='.urlencode($encrypted), $ttl_back);
	//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key2), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key2))), "\0");
	$decrypted = kh_decrypt($response, $key2);
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ok"){
	//update succesful
	}else{
	$info=time().'**error**could not remote sync meeting start**'.$mount.'@'.$server.':'.$port.'**'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}
	
	$info=time().'**info**new mount**'.$mount."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong) $type=$data[5];
	}
		if ($type=="none" OR $type=='direct-stream' OR $type=='jitsi'){
		$file=fopen($temp_dir.'meeting_'.$cong,'w');
			fputs($file,"live");
		fclose($file);
		}
	}else{
	$info=time().'**warn**new mount**'.$mount.'@'.$server.':'.$port."**no cong linked to stream**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	
	}
	$info=$mount.'**'.time()."**\n";
	$file=fopen('./db/live_streams','a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}
 }
?>