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
		if ($_POST['id_confirmed']!=""){
			$id_confirmed=urldecode($_POST['id_confirmed']);//sanitize
		$deleting=timing_del($id_confirmed);
if ($deleting=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**timing del successful**'.$id_confirmed."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $deleting;
$info=time().'**error**timing del fail**'.$id_confirmed."**\n";
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
if(isset($_GET['id'])){
$id=$_GET['id']; //sanitize input
?>
<div id="page">
<h2>Timing</h2>
Are you sure you want to remove this timing?<br /><br />
<form action="./timing_delete" method="post">
<b>ID</b>: <?PHP echo $id;?><br />
<input type="hidden" name="id_confirmed" value="<?PHP echo $id;?>"><br />

<a href="./timings"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Timing</h2>
Click <a href="./timings">here</a> to view the timings.<br /><br />
</div>
<?PHP
}
?>