<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$skip=0;
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['delete']){
		if ($_POST['user_confirmed']!=""){
			$user_confirmed=urldecode($_POST['user_confirmed']);//sanitize
			$pin=$_POST['pin'];
			$db=file("db/users");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		
		if ($data[0]==$user_confirmed){
			if ($data[3]==$_SESSION['cong'] OR $_SESSION['type']=='root'){
			$congregation=$data[3];
			}else{
			//this an attempt at deleting a user from another cong - log
			$file_content.=$line;
			$skip=1;
			}
		}else{
		$file_content.=$line;
		}
		
	}
	if ($skip==0){
			$file=fopen('./db/users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			exec($asterisk_bin.' -rx "database del '.$congregation.' '.$pin.'"');
			//remove voip account if needed
include "sip-gen.php";

include "iax-gen.php";
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
		}else{
		//this an attempt at deleting a user from another cong - log
		echo '<div id="error_msg">'.$lng['error'].'</div>';
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
