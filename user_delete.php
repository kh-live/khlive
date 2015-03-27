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

<a href="./users"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
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
