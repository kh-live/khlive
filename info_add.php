<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include '404.php';
exit(); 
}
include 'functions.php';
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['congregation']!="0"){
		//we obviously need to check the input
			$congregation=$_POST['congregation']; //check
			$infos=str_replace( "\n", '<br />',$_POST['infos']);
			$link=$_POST['link'];
			$enable=$_POST['enable'];
			
$adding=info_add($congregation,$infos,$link,$enable);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**new info add successful**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**new info add fail**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'...</div>';
		}
	} ?>
	<div id="page">
<h2>Notice Board</h2>
Click <a href="./infos">here</a> to view the notice board.<br /><br />
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Notice Board</h2>
Add new information<br /><br />
<form action="./info_add" method="post">
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
if ($_SESSION['type']=='root'){
echo '<option value="all">ALL</option>';
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
<b>Information</b><br />
<textarea cols="70" rows="5" name="infos"></textarea><br /><br />
<b>Link</b><br />
If it's not a link, leave blank<br />
<input type="text" size="70" name="link" /><br /><br />
<b>Enable this information</b><br />
<select name="enable">
<option value="yes">yes</option>
<option value="no">no</option>
</select><br /><br />
<br />
<br /><br />
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP } ?>
