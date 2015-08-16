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
		if ($_POST['cong_confirmed']!=""){
			$cong_confirmed=urldecode($_POST['cong_confirmed']);//sanitize
		$deleting=cong_del($cong_confirmed, "del");
if ($deleting=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**local cong del successful**'.$cong_confirmed."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $deleting;
$info=time().'**error**local cong del fail**'.$cong_confirmed."**\n";
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
/*
we must still delete the logs and the recordings. plus we must check if there is no users associated with the cong. we must also get the code more readable avoid long if{}  etc...
*/
if(isset($_GET['cong'])){
$cong=$_GET['cong']; //sanitize input
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
<?PHP echo $lng['remove_cong'];?><br /><br />
<form action="./cong_delete" method="post">
<b><?PHP echo $lng['congregation'];?></b>: <?PHP echo $cong;?><br />
<input type="hidden" name="cong_confirmed" value="<?PHP echo $cong;?>"><br />

<a href="./congregations"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
Click <a href="./congregations">here</a> to edit more congregations.<br /><br />
</div>
<?PHP
}
?>