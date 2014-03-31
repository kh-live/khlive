<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

$error="";
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['rights']!="0" AND $_POST['congregation']!="0" AND $_POST['user']!="" AND $_POST['password']!="" AND strlen($_POST['password'])>=8 AND $_POST['name']!="" AND $_POST['pin']>=9999 AND $_POST['pin']<=100000){
			$db=file("db/users");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$_POST['user']) $error="ko";
			if ($data[5]==$_POST['pin']) $error="ko";
			}
			if ($error!="ko"){
			
			$salt=hash("sha512",rand());
			$pwd_hashed=hash("sha512",$salt.$_POST['password']);
			$info=$_POST['user'].'**'.$salt.'--'.$pwd_hashed.'**'.$_POST['name'].'**'.$_POST['congregation'].'**'.$_POST['rights'].'**'.$_POST['pin'].'**'.$_POST['type']."** **\n"; //sanitize input
			$file=fopen('./db/users','a');
			if(fputs($file,$info)){
			fclose($file);
			
			//add account for voip if needed
			exec('asterisk -rx "database put '.$_POST['congregation'].' '.$_POST['pin'].' '.$_POST['user'].'"');
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'...</div>';
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
<input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>">
</form>
</div>
