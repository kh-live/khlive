<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['old_server_name']!=""){
			$server_confirmed=urldecode($_POST['old_server_name']);//sanitize
			$db=file("db/servers");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$server_confirmed){
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/servers','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
	
			
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
				$error="";
			$db=file("db/servers");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$_POST['server_name']) $error="ko";
			}
			if ($error==""){
			$server_name=$_POST['server_name']; //check
			$server_url=$_POST['server_url'];
			$server_api=$_POST['server_api'];
			$congs=$_POST['congs'];
			$localip=$_POST['localip'];
			
			$info=$server_name."**".$server_url."**".$server_api."**".$congs."**".$localip."**\n";

			$file=fopen('./db/servers','a');
			if(fputs($file,$info)){
			fclose($file);
			echo 'success!';
			}
}else{
echo 'error : server already exists';
}
	}elseif ($_POST['submit']=='Fetch user db'){
	$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$_POST['old_server_name']){
	$key=$data[2];
	$string=time()."**fetch_users";
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://'.$data[1].'/kh-live/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ok"){
	$user_db=$dec[0];
	$file=fopen('./db/tmp_users','w');
			if(fputs($file,$user_db)){
			fclose($file);
			echo 'success!';
			}

	}else{
	echo 'error wrong answer : '.$decrypted;
	}
	}
	}
	$users = array();
	$new_users="";
	$db=file('db/users');
	    foreach($db as $line){
        $data=explode ("**",$line);
	//we store all current users in a table
	$users[$data[0]]=$data[3];
	}
	$db=file('db/tmp_users');
	 foreach($db as $line){
        $data=explode ("**",$line);
		if (!isset($users[$data[0]])){
		$new_users.=$line;	
		}else{
		echo 'User : '.$data[0].' alreday in db. From cong : '.$users[$data[0]].' Skipped!';
		}
	}
	$file=fopen('db/users','a');
			if(fputs($file,$new_users)){
			fclose($file);
			echo 'success!';
			unlink('db/tmp_users');
			}
	
	}elseif ($_POST['submit']=='Fetch cong db'){
	$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$_POST['old_server_name']){
	$key=$data[2];
	$string=time()."**fetch_congs";
	$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$response=file_get_contents('http://'.$data[1].'/kh-live/api.php?q='.urlencode($encrypted));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ok"){
	$user_db=$dec[0];
	$file=fopen('./db/tmp_congs','w');
			if(fputs($file,$user_db)){
			fclose($file);
			echo 'success!';
			}

	}else{
	echo 'error wrong answer : '.$decrypted;
	}
	}
	}
	$users = array();
	$new_users="";
	$db=file('db/cong');
	    foreach($db as $line){
        $data=explode ("**",$line);
	//we store all current users in a table
	$users[$data[0]]=$data[1];
	}
	$db=file('db/tmp_congs');
	 foreach($db as $line){
        $data=explode ("**",$line);
		if (!isset($users[$data[0]])){
		$new_users.=$line;	
		}else{
		echo 'Cong : '.$data[0].' alreday in db. Pin : '.$users[$data[0]].' Skipped!';
		}
	}
	$file=fopen('db/cong','a');
			if(fputs($file,$new_users)){
			fclose($file);
			echo 'success!';
			unlink('db/tmp_congs');
			}
	
	}
}

if(isset($_GET['server'])){
$server=$_GET['server']; //sanitize input
$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$server){
	$url=$data[1];
	$key=$data[2];
	$congs=@$data[3];
	$localip=@$data[4];
	}
	}
?>
<div id="page">
<h2>SERVERS</h2>
Edit server<br /><br />
<form action="./server_edit" method="post">
<b>server</b>: <?PHP echo $server;?><br />
<b>server name</b><br />
<input class="field_login" type="text" name="server_name" value="<?PHP echo @$server; ?>" />
<br /><br />
<b>server url</b><br />
<input class="field_login" type="text" name="server_url" value="<?PHP echo @$url; ?>" />
<br /><br />
<b>server api</b><br />
<input class="field_login" type="text" name="server_api" value="<?PHP echo @$key; ?>" />
<br /><br />
<b>congregations linked to server (separate with ";")</b><br />
<input class="field_login" type="text" name="congs" value="<?PHP echo @$congs; ?>" />
<br /><br />
<b>server local ip address (192.168.1.123 or similar)</b><br />
<input class="field_login" type="text" name="localip" value="<?PHP echo @$localip; ?>" />
<br /><br />
<input type="hidden" name="old_server_name" value="<?PHP echo @$server; ?>" />
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
<input name="submit" type="submit" value="<?PHP echo 'Fetch user db';?>" /><input name="submit" type="submit" value="<?PHP echo 'Fetch cong db';?>" />
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2>SERVERS</h2>
Click <a href="./servers">here</a> to edit more servers.<br /><br />
</div>
<?PHP
}
?>