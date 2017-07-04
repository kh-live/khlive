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
			$start_time=$_POST['start_time_hour'].':'.$_POST['start_time_min'];
			$stop_time=$_POST['stop_time_hour'].':'.$_POST['stop_time_min'];
			$enable=$_POST['enable'];
			$timing=$_POST['timing'];
			
	$adding=sched_add($congregation,$day,$start_time,$stop_time,$enable,$timing);

if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**schedule meeting edit successful**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**schedule meeting edit add fail**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
}else{
echo $deleting;
$info=time().'**error**schedule meeting edit del fail**'.$congregation."**\n";
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
	$start_time=explode(':',$data[2]);
	$start_time_hour=$start_time[0];
	$start_time_min=$start_time[1];
	$stop_time=explode(':',$data[3]);
	$stop_time_hour=$stop_time[0];
	$stop_time_min=$stop_time[1];
	$enabled=$data[4];
	$timing=@$data[5];
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
	echo '<option ';
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
<select name="start_time_hour">
<option value="0">...</option>
<?PHP
for ($i=0; $i<=23; $i++){
	echo '<option ';
	if ($start_time_hour==$i) echo 'selected="selected" ';
	echo 'value="'.$i.'">'.$i.'</option>';
}
?>
</select>h
<select name="start_time_min">
<option value="0">...</option>
<?PHP
	for ($j=0; $j<=55; $j+=5){
	echo '<option ';
	if ($start_time_min==$j) echo 'selected="selected" ';
	echo 'value="'.$j.'">';
	if ($j==0) echo $j;
	if ($j==5) echo '0';
	echo $j.'</option>';
	}
?>
</select>min<br /><br />
<b>Stop Time</b><br />
<select name="stop_time_hour">
<option value="0">...</option>
<?PHP
for ($i=0; $i<=23; $i++){
	echo '<option ';
	if ($stop_time_hour==$i) echo 'selected="selected" ';
	echo 'value="'.$i.'">'.$i.'</option>';
}
?>
</select>h
<select name="stop_time_min">
<option value="0">...</option>
<?PHP
	for ($j=0; $j<=55; $j+=5){
	echo '<option ';
	if ($stop_time_min==$j) echo 'selected="selected" ';
	echo 'value="'.$j.'">';
	if ($j==0) echo $j;
	if ($j==5) echo '0';
	echo $j.'</option>';
	}
?>
</select>min<br /><br />
<b>Timing</b><br />
<select name="timing">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
$db=file("db/timings");
    foreach($db as $line){
        $data=explode ("**",$line);
	$total_length=0;
	$timings=unserialize($data[1]);
	foreach ($timings as $key=>$value){
		if (strstr($key,'length')){
		$total_length+=$value;
		}
		}
	echo '<option ';
	if ($timing==$data[0]) echo 'selected="selected" ';
	echo 'value="'.$data[0].'">'.$data[0].' ('.$total_length.'min)</option>';
	}
	
?>
</select><br /><br />
<b>Enable this meeting</b><br />
<select name="enable">
<option value="yes" <?PHP if ($enabled=='yes') echo ' selected="selected" ';?>>yes</option>
<option value="no" <?PHP if ($enabled=='no') echo ' selected="selected" ';?>>no</option>
</select>
<br /><br />
<input type="hidden" name="id_confirmed" value="<?PHP echo $id;?>">
<a href="./scheduler">cancel</a> <input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
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