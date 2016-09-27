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
$deleting=sched_del($id_confirmed);
if ($deleting=='ok'){
$congregation=$_POST['congregation']; //check
			$day=$_POST['day'];
			$start_time=$_POST['start_time'];
			$stop_time=$_POST['stop_time'];
			$enable=$_POST['enable'];
			
	$adding=sched_add($congregation,$day,$start_time,$stop_time,$enable);

if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**schedule meeting edit successful**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**schedule meeting edit add fail**'.$cong_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
}else{
echo $deleting;
$info=time().'**error**schedule meeting edit del fail**'.$cong_confirmed."**\n";
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
$db=file("db/sched");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($i==$id) {
	$cong_selected=$data[0];
	$day=$data[1];
	$start_time=$data[2];
	$stop_time=$data[3];
	$enabled=$data[4];
	}
	$i++;
	}
	
?>
<div id="page">
<h2>Scheduler</h2>
Edit scheduled meeting<br /><br />
<form action="./sched_edit" method="post">
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<?PHP
if ($_SESSION['type']=='root'){
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<option';
	if ($data[0]==$cong_selected) echo ' selected="selected" ';
		echo 'value="'.$data[0].'">'.$data[0].'</option>';
	}
	}else{
	echo '<option value="'.$_SESSION['cong'].'">'.$_SESSION['cong'].'</option>';
	}
?>
</select><br /><br />
<b>Day of the week</b><br />
<select name="day">
<option value="Mon" <?PHP if ($day=='Mon') echo ' selected="selected" ';?>>Monday</option>
<option value="Tue" <?PHP if ($day=='Tue') echo ' selected="selected" ';?>>Tuesday</option>
<option value="Wed" <?PHP if ($day=='Wed') echo ' selected="selected" ';?>>Wednesday</option>
<option value="Thu" <?PHP if ($day=='Thu') echo ' selected="selected" ';?>>Thursday</option>
<option value="Fri" <?PHP if ($day=='Fri') echo ' selected="selected" ';?>>Friday</option>
<option value="Sat" <?PHP if ($day=='Sat') echo ' selected="selected" ';?>>Saturday</option>
<option value="Sun" <?PHP if ($day=='Sun') echo ' selected="selected" ';?>>Sunday</option>
</select><br /><br />
<b>Start Time</b><br />
<select name="start_time">
<option value="0">...</option>
<?PHP
for ($i=0; $i<=23; $i++){
	for ($j=0; $j<=45; $j+=15){
	echo '<option ';
	if ($start_time==$i.':'.$j) echo ' selected="selected" ';
	echo 'value="'.$i.':'.$j.'">'.$i.':'.$j;
	if ($j==0) echo $j;
	echo '</option>';
	}
}
?>
</select><br /><br />
<b>Stop Time</b><br />
<select name="stop_time">
<option value="0">...</option>
<?PHP
for ($i=0; $i<=23; $i++){
	for ($j=0; $j<=45; $j+=15){
	echo '<option ';
	if ($stop_time==$i.':'.$j) echo ' selected="selected" ';
	echo 'value="'.$i.':'.$j.'">'.$i.':'.$j;
	if ($j==0) echo $j;
	echo '</option>';
	}
}
?>
</select><br /><br />
<b>Enable this meeting</b><br />
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
<h2>Scheduler</h2>
Click <a href="./scheduler">here</a> to view the schedule.<br /><br />
</div>
<?PHP
}
?>