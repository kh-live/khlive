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
		if ($_POST['user_confirmed']!="" AND $_POST['congregation']!="0" AND $_POST['rights']!="0" AND $_POST['user']!="" AND $_POST['name']!="" AND $_POST['pin']>=9999 AND $_POST['pin']<=100000 AND (strlen($_POST['password'])>=8 OR $_POST['password']=='')){
			$user_confirmed=urldecode($_POST['user_confirmed']);
			$user_new=$_POST['user'];//sanitize input
			$congregation_new=$_POST['congregation'];//sanitize input
			$name_new=$_POST['name'];//sanitize input
			$password_new=$_POST['password'];//sanitize input
			$rights_new=$_POST['rights'];//sanitize input
			$pin=$_POST['pin'];//sanitize input
			$old_pin=$_POST['old_pin'];//sanitize input
			$old_cong=$_POST['old_cong'];//sanitize input
			$type_new=$_POST['type'];//sanitize input
			$info_new=$_POST['info'];//sanitize input
			$last_login=time();
			$error="";
			if ($user_new!=$user_confirmed){
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$user_new) $error="ko";
			}
			}
			if ($pin!=$old_pin){
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			 if ($data[5]==$pin) $error="ko";
			}
			}
			if ($error!="ko"){
			$encode="1";
			if ($password_new==""){
			$encode="0";
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$user_confirmed){
			$password_new=$data[1];
			}
			}
			}
$deleting=kh_user_del($user_confirmed,$old_pin);
if ($deleting=='ok'){
$adding=kh_user_add($user_new,$password_new,$name_new,$congregation_new,$rights_new,$pin,$type_new,$last_login,$info_new,$encode);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**local user edit successful**'.$user_confirmed.'@'.$old_cong.'->'.$user_new.'@'.$congregation_new."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**local user edit fail add**'.$user_confirmed.'@'.$old_cong.'->'.$user_new.'@'.$congregation_new."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
}else{
echo $deleting;
$info=time().'**error**local user edit fail del**'.$user_confirmed.'@'.$old_cong.'->'.$user_new.'@'.$congregation_new."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
			
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'</div>';
		}
	}
}
if(isset($_GET['user'])){
$user=urldecode($_GET['user']); //sanitize input
$congregation="";
$password="";
$name="";
$rights="";
$pin="";
$type="";
$info="";
$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$user) {	
	$name=$data[2];
	$congregation=$data[3];
	$rights=$data[4];
	$pin=$data[5];
	$type=$data[6];
	$info=@$data[8];
	}
	}
?>
<div id="page">
<h2><?PHP echo $lng['users'];?></h2>
<?PHP echo $lng['edit_user'];?><br /><br />
<form action="./user_edit" method="post">
<b><?PHP echo $lng['name'];?></b><br />
User's real full name.<br />
<input class="field_login" type="text" name="name" value="<?PHP echo $name;?>"><br /><br />
<b>Info</b><br />
Information about the user.<br />
<input class="field_login" type="text" name="info" value="<?PHP echo $info;?>"><br /><br />
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
if ($_SESSION['type']=='root'){
$db=file("db/cong");
    foreach($db as $line){
    $selected="";
        $data=explode ("**",$line);
	if ($data[0]==$congregation) $selected="selected=selected";
	echo '<option value="'.$data[0].'" '.$selected.'>'.$data[0].'</option>';
	}
	}else{
	echo '<option value="'.$_SESSION['cong'].'" selected=selected>'.$_SESSION['cong'].'</option>';
	}
?>
</select><br /><br />
<input type="hidden" name="old_cong" value="<?PHP echo $congregation;?>">
<b><?PHP echo $lng['rights'];?></b><br />
<select name="rights">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
$db=array("admin","manager","user","multi");
    foreach($db as $line){
    $selected="";
	if ($line==$rights) $selected="selected=selected";
	$tmp="user_".$line;
	echo '<option value="'.$line.'" '.$selected.'>'.$lng[$tmp].'</option>';
	}
if ($_SESSION['type']=='root'){
 $selected="";
	if ($rights=='root') $selected="selected=selected";
echo '<option value="root" '.$selected.'>Root</option>';
}
?>
</select><br /><br />
<b>Access Type</b><br />
voip : listening only through voip client (kiax/yate/zoiper) - no web access<br />
web : listening only on the web streaming - no voip account is created<br />
all : access via voip or web<br />
<select name="type">
<option value="web" <?PHP if ($type=="web") echo "selected=selected"; ?>>web</option>
<option value="voip" <?PHP if ($type=="voip") echo "selected=selected"; ?>>voip</option>
<option value="all" <?PHP if ($type=="all") echo "selected=selected"; ?>>all</option>
</select><br /><br />
<b><?PHP echo $lng['user'];?></b><br />
User's account name (used to login)<br />
<input class="field_login" type="text" name="user" value="<?PHP echo $user;?>"><br />
<b><?PHP echo $lng['password'];?></b><br />
Leave blank if no change. At least 8 characters. Tip : use a sentence!<br />
<input class="field_login" type="password" name="password" value="<?PHP echo $password;?>"><br />
<b><?PHP echo $lng['PIN'];?></b><br />
5 numbers. Generated Automaticaly. Used to login when calling on the trunk (if enabled).<br />
<input class="field_login" type="text" name="pin" value="<?PHP echo $pin;?>">#<br />
<input type="hidden" name="old_pin" value="<?PHP echo $pin;?>">
<input type="hidden" name="user_confirmed" value="<?PHP echo $user;?>">
<a href="./users"><?PHP echo $lng['cancel'];?></a> 
<?PHP
if ($auto_khlive=='yes' OR $server_beta=='master'){
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
	if ($server_beta=='master'){
		$url="";
		$db=file("db/servers");
		foreach($db as $line){
			$data=explode ("**",$line);
			if (strstr($data[3],$_SESSION['cong'])){
				$url=$data[1];
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
		$test_time=kh_fgetc_timeout($q_proto.$url.$q_port.'/kh-live/time.php');
		}
	}else{
		$test_time=kh_fgetc_timeout('https://kh-live.co.za/time.php');
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
	3. Ask your administrator to reboot the server</b><br />';
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
