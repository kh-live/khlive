<?PHP
$string="";
include ("db/config.php");
if (isset($_GET['q'])){
$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($api_key), base64_decode($_GET['q']), MCRYPT_MODE_CBC, md5(md5($api_key))), "\0");
$query=explode("**", $decrypted);
	if ($query[0]+60>=time()){
		if ($query[1]=="status"){
		$string="ok";
		}elseif($query[1]=="fetch_users"){
		$db=file('db/users');
			foreach($db as $line){
			$string.=$line;
			}
			$string.="@@@ok";
		}
	
	if ($string!=""){
	$string=="nothing@@@ko";
		}
	}else{
	$string=="timeout@@@".time();
	}
		$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($api_key), $string, MCRYPT_MODE_CBC, md5(md5($api_key))));
		echo $encrypted;
		exit;
}
//$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
?>