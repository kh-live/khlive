<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit();
}
$skip=0;
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['user_confirmed']!="" AND $_POST['congregation']!="0" AND $_POST['rights']!="0" AND $_POST['user']!="" AND $_POST['name']!="" AND $_POST['pin']>=9999 AND $_POST['pin']<=100000){
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
			$db=file("db/users");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$user_confirmed){
		 if ($data[3]==$_SESSION['cong'] OR $_SESSION['type']=='root'){
			if ($password_new==""){
			$password_new=$data[1];
			}else{
			$salt=hash("sha512",rand());
			$pwd_hashed=hash("sha512",$salt.$password_new);
			$password_new=$salt."--".$pwd_hashed;
			}
		$file_content.=$user_new.'**'.$password_new.'**'.$name_new.'**'.$congregation_new.'**'.$rights_new.'**'.$pin.'**'.$type_new."** **".$info_new."**\n";
		 }else{
			//this an attempt at editing a user from another cong - log
			$file_content.=$line;
			$skip=1;
			}
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			
		if ($skip==0){
			if ($old_cong!=$congregation_new OR $old_pin!=$pin){
			exec($asterisk_bin.' -rx "database del '.$old_cong.' '.$old_pin.'"');
			exec($asterisk_bin.' -rx "database put '.$congregation_new.' '.$pin.' '.$user_new.'"');
			}
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			
			//add voip account if needed
include "sip-gen.php";

include "iax-gen.php";			
			}else{
		//this an attempt at deleting a user from another cong - log
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
		}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
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
$db=array("admin","manager","user");
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
<a href="./users"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>">
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
