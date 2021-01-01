<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include './functions.php';

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
			$s_enable_ssl=$_POST['s_enable_ssl'];
			$http_port=$_POST['http_port'];
			$https_port=$_POST['https_port'];
			$localip=$_POST['localip'];
			
			$info=$server_name."**".$server_url."**".$server_api."**".$congs."**".$localip."**".$s_enable_ssl."**".$http_port."**".$https_port."**\n";

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
	//$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$encrypted=kh_encrypt($string,$key);
	$q_proto='http://';
	$q_port=':80';
	if (@$data[6]!='' AND is_numeric($data[6])) $q_port=':'.$data[6];
	if (@$data[5]=='auto' OR @$data[5]=='force'){
		$q_proto='https://';
		$q_port=':443';
		if (@$data[7]!='' AND is_numeric($data[7])) $q_port=':'.$data[7];
	}
	$response=kh_fgetc_timeout($q_proto.$data[0].$q_port.'/kh-live/api.php?q='.urlencode($encrypted), 10);
	//$response=file_get_contents('http://'.$data[1].'/kh-live/api.php?q='.urlencode($encrypted));
	//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	$decrypted = kh_decrypt($response, $key);
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
	//$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$encrypted=kh_encrypt($string,$key);
	$q_proto='http://';
	$q_port=':80';
	if (@$data[6]!='' AND is_numeric($data[6])) $q_port=':'.$data[6];
	if (@$data[5]=='auto' OR @$data[5]=='force'){
		$q_proto='https://';
		$q_port=':443';
		if (@$data[7]!='' AND is_numeric($data[7])) $q_port=':'.$data[7];
	}
	$response=kh_fgetc_timeout($q_proto.$data[0].$q_port.'/kh-live/api.php?q='.urlencode($encrypted), 10);
	//$response=file_get_contents('http://'.$data[1].'/kh-live/api.php?q='.urlencode($encrypted));
	//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	$decrypted = kh_decrypt($response, $key);
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
	$s_enable_ssl=@$data[5];
	$http_port=@$data[6];
	$https_port=@$data[7];
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
<b>Enable SSL for this server</b><br />
<select class="field_login" name="s_enable_ssl" >
<option value="no" <?PHP if (@$s_enable_ssl=="no") echo 'selected=selected';?>>no (default)</option>
<option value="force" <?PHP if (@$s_enable_ssl=="force") echo 'selected=selected';?>>force</option>
<option value="auto" <?PHP if (@$s_enable_ssl=="auto") echo 'selected=selected';?>>auto</option>
</select>
<br /><br />
<i style="color:red;">If you decide to use a non standard ports, the SSL certificate won't renew automatically!</i><br /><br />
<b>server http port (default 80)</b><br />
<input class="field_login" type="text" name="http_port" value="<?PHP echo @$http_port; ?>" />
<br /><br />
<b>server https port (default 443)</b><br />
<input class="field_login" type="text" name="https_port" value="<?PHP echo @$https_port; ?>" />
<br /><br />
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