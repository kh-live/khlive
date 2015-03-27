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
	$last_login=" ";
	$encode="1";
	$adding=kh_user_add($user,$password,$name,$congregation,$rights,$pin,$type,$last_login,$info,$encode);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
}else{
echo $adding;
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
