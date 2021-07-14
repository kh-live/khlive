<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include 'functions.php';

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
	$user=$_POST['user'];
	$password=$_POST['password'];
	$name=$_POST['name'];
	$congregation=$_POST['congregation'];
	$rights=$_POST['rights'];
	$pin=$_POST['pin'];
	$type=$_POST['type'];
	$info=$_POST['info'];
	$last_login=time();
	$encode="1";
	$adding=kh_user_add($user,$password,$name,$congregation,$rights,$pin,$type,$last_login,$info,$encode);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**local user add successful**'.$user.'@'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**local user add fail**'.$user.'@'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
	}
}
?>
<div id="page">
<h2><?PHP echo $lng['users'];?></h2>
<?PHP echo $lng['add_new_user'];?><br /><br />
<form action="./user_add" method="post">
<b><?PHP echo $lng['name'];?></b><br />
User's real full name.<br />
<input class="field_login" type="text" name="name"><br /><br />
<b>Info</b><br />
Information about the user.<br />
<input class="field_login" type="text" name="info"><br /><br />
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
if ($_SESSION['type']=='root'){
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<option value="'.$data[0].'">'.$data[0].'</option>';
	}
	}else{
	echo '<option value="'.$_SESSION['cong'].'">'.$_SESSION['cong'].'</option>';
	}
?>
</select><br /><br />
<b><?PHP echo $lng['rights'];?></b><br />
<select name="rights">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<option value="admin"><?PHP echo $lng['user_admin'];?></option>
<option value="manager"><?PHP echo $lng['user_manager'];?></option>
<option value="user"><?PHP echo $lng['user_user'];?></option>
<option value="multi"><?PHP echo $lng['user_multi'];?></option>
<?PHP
if ($_SESSION['type']=='root'){
echo '<option value="root">Root</option>';
}
?>
</select><br /><br />
<b>Access Type</b><br />
voip : listening only through voip client (kiax/yate/zoiper) - no web access<br />
web : listening only on the web streaming - no voip account is created<br />
all : access via voip or web<br />
<select name="type">
<option value="web">web</option>
<option value="voip">voip</option>
<option value="all">all</option>
</select><br /><br />
<b><?PHP echo $lng['user'];?></b><br />
User's account name (used to login)<br />
<input class="field_login" type="text" name="user"><br /><br />
<b><?PHP echo $lng['password'];?></b><br />
At least 8 characters. Tip : use a sentence!<br />
<input class="field_login" type="password" name="password"><br /><br />
<b><?PHP echo $lng['PIN'];?></b><br />
5 numbers. Generated Automaticaly. Used to login when calling on the trunk (if enabled).<br />
<input class="field_login" type="text" name="pin" value="<?PHP echo rand(10000,99999) ;?>">#<br /><br />
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
<input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>">
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
	3. Ask your administrator to reboot the server<br />
	4. Set the TTL higher in config>kh-live>TTL default (currently set to '.$ttl_back.'sec)</b><br />';
	}
}else{
?>
<input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>"><br /> 
<b style="color:red">Warning! The changes are not synchronised with kh-live.co.za!</b><br />
If this is unexpected change the Auto config kh-live.co.za setting on configuration page.<br />
<?PHP
}
?>
</form>
</div>
