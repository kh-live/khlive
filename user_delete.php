<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include 'functions.php';
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['delete']){
		if ($_POST['user_confirmed']!=""){
			$user_confirmed=urldecode($_POST['user_confirmed']);//sanitize
			$pin=$_POST['pin'];
			$deleting=kh_user_del($user_confirmed,$pin);
if ($deleting=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**local user delete successful**'.$user_confirmed."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $deleting;
$info=time().'**error**local user delete fail**'.$user_confirmed."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
	}
}
if(isset($_GET['user'])){
$user=$_GET['user']; //sanitize input
$pin=$_GET['pin']; //sanitize input

?>
<div id="page">
<h2><?PHP echo $lng['users'];?></h2>
<?PHP echo $lng['remove_user'];?><br /><br />
<form action="./user_delete" method="post">
<b><?PHP echo $lng['user'];?></b>: <?PHP echo $user;?><br />
<input type="hidden" name="user_confirmed" value="<?PHP echo $user;?>"><br />
<input type="hidden" name="pin" value="<?PHP echo $pin;?>"><br />

<a href="./users"><?PHP echo $lng['cancel'];?></a> 
<?PHP
if ($auto_khlive=='yes' OR $server_beta=='master'){
	if ($server_beta=='master'){
		$url="";
		$db=file("db/servers");
		foreach($db as $line){
			$data=explode ("**",$line);
			if (strstr($data[3],$_SESSION['cong'])){
				$url=$data[0];
				$q_proto='http://';
				$q_port=':80';
				if ($data[6]!='' AND is_numeric($data[6])) $q_port=':'.$data[6];
				if ($data[5]=='auto' OR $data[5]=='force'){
					$q_proto='https://';
					$q_port=':443';
					if ($data[7]!='' AND is_numeric($data[7])) $q_port=':'.$data[7];
				}
			}
		}
		if ($url==""){
		echo 'Could not find your congregations server...';
		}else{
		$test_time=kh_fgetc_timeout($q_proto.$url.$q_port.'/kh-live/time.php', $ttl_back);
		}
	}else{
		$test_time=kh_fgetc_timeout($https.'://kh-live.co.za/time.php', $ttl_back);
	}
if ($test_time!==FALSE){
	if (is_numeric($test_time)){
		$now=time();
		$min=$now - 60 ;
		$max=$now + 60;
		if (($min <= $test_time) AND ($test_time <= $max)){
?>
<input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
<?PHP
		}else{
			echo '<br /><b style="background-color:orange;color:black;display:block;">Error : The clock is not synchronised!</b><br />';
		}
	}else{
		echo '<br /><b style="background-color:orange;color:black;display:block;">Error : Cant get remote time!</b><br />';
	}
}else{
	echo '<br /><b style="background-color:red;color:white;display:block;">Warning! we cant connect to the remote server.<br />
	You cant manage users while offline.<br />
	Check the following : <br />
	1. Your databundle is not finished<br />
	2. Reboot the router<br />
	3. Ask your administrator to reboot the server</b><br />
	4. Set the TTL higher in config>kh-live>TTL default (currently set to '.$ttl_back.'sec)<br />';
	}
}else{
?>
<input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>"><br /> 
<b style="color:red">Warning! The changes are not synchronised with kh-live.co.za!</b><br />
If this is unexpected change the Auto config kh-live.co.za setting on configuration page.<br />
<?PHP
}
?>
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['users'];?></h2>
Click <a href="./users">here</a> to edit more users.<br /><br />
</div>
<?PHP
}
?>
