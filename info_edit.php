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
		if ($_POST['id_confirmed']!=""){
		//start cong del
			$id_confirmed=urldecode($_POST['id_confirmed']);//sanitize
$deleting=info_del($id_confirmed);
if ($deleting=='ok'){
$congregation=$_POST['congregation']; //check
			$infos=str_replace( "\n", '<br />',$_POST['infos']);
			$link=$_POST['link'];
			$enable=$_POST['enable'];
			
	$adding=info_add($congregation,$infos,$link,$enable);

if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**info edit successful**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**info edit add fail**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
}else{
echo $deleting;
$info=time().'**error**info edit del fail**'.$congregation."**\n";
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
$id=urldecode($_GET['id']); //sanitize input
$db=file("db/infos");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($i==$id) {
	$cong_selected=$data[0];
	$infos=$data[1];
	$link=$data[2];
	$enabled=$data[3];
	}
	$i++;
	}
	
?>
<div id="page">
<h2>Notice Board</h2>
Edit information<br /><br />
<form action="./info_edit" method="post">
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<?PHP
if ($_SESSION['type']=='root'){
echo '<option value="all">ALL</option>';
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<option ';
	if ($data[0]==$cong_selected) echo ' selected="selected" ';
		echo 'value="'.$data[0].'">'.$data[0].'</option>';
	}
	}else{
	echo '<option value="'.$_SESSION['cong'].'">'.$_SESSION['cong'].'</option>';
	}
?>
</select><br /><br />
<b>Information</b><br />
<textarea cols="70" rows="5" name="infos"><?PHP echo str_replace( '<br />', "\n" ,$infos) ; ?></textarea><br /><br />
<b>Link</b><br />
If it's not a link, leave blank<br />
<input type="text" size="70" name="link" value="<?PHP echo $link ; ?>" /><br /><br />
<b>Enable this information</b><br />
<select name="enable">
<option value="yes" <?PHP if ($enabled=='yes') echo ' selected="selected" ';?>>yes</option>
<option value="no" <?PHP if ($enabled=='no') echo ' selected="selected" ';?>>no</option>
</select><br /><br />
<br />
<br /><br />
<input type="hidden" name="id_confirmed" value="<?PHP echo $id;?>">
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Notice Board</h2>
Click <a href="./infos">here</a> to view the notice board.<br /><br />
</div>
<?PHP
}
?>
