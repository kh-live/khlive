<?PHP
$string="";
include ("db/config.php");
if (isset($_GET['q'])){
$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($api_key), base64_decode($_GET['q']), MCRYPT_MODE_CBC, md5(md5($api_key))), "\0");
$query=explode("**", $decrypted);
	if ($query[0]+60>=time()){
		if ($query[1]=="status"){
		$string=$version."@@@ok";
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
		$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$server_name){
	$api_key=$data[2];
		$content.=$server_name."**".$new_ip."**".$api_key."**\n";
		}else{
		$content.=$line;
		}
		}
		$file=fopen("db/servers",'w');
	if (fputs($file,$content)){
	fclose($file);
		$string=$new_ip."@@@ok";
		}
		}
	
	if ($string!=""){
	$string=="nothing@@@ko";
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
	$string=="timeout@@@".time();
	}
		$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($api_key), $string, MCRYPT_MODE_CBC, md5(md5($api_key))));
		echo $encrypted;
		exit;
}
//$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
?>