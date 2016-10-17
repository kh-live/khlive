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
			$day=$_POST['day'];
			$start_time=$_POST['start_time_hour'].':'.$_POST['start_time_min'];
			$stop_time=$_POST['stop_time_hour'].':'.$_POST['stop_time_min'];
			$enable=$_POST['enable'];
			
$adding=sched_add($congregation,$day,$start_time,$stop_time,$enable);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**new schedule meeting add successful**'.$congregation."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**new schedule meeting add fail**'.$congregation."**\n";
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
<h2>Scheduler</h2>
Click <a href="./scheduler">here</a> to view the scheduler.<br /><br />
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Scheduler</h2>
Add new scheduled meeting<br /><br />
<form action="./sched_add" method="post">
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
<b>Day of the week</b><br />
<select name="day">
<option value="0">...</option>
<option value="Mon">Monday</option>
<option value="Tue">Tuesday</option>
<option value="Wed">Wednesday</option>
<option value="Thu">Thursday</option>
<option value="Fri">Friday</option>
<option value="Sat">Saturday</option>
<option value="Sun">Sunday</option>
</select><br /><br />
<b>Start Time</b><br />
<select name="start_time_hour">
<option value="0">...</option>
<?PHP
for ($i=0; $i<=23; $i++){
	echo '<option value="'.$i.'">'.$i.'</option>';
}
?>
</select>h
<select name="start_time_min">
<option value="0">...</option>
<?PHP
	for ($j=0; $j<=55; $j+=5){
	echo '<option value="'.$j.'">';
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
	echo '<option value="'.$i.'">'.$i.'</option>';
}
?>
</select>h
<select name="stop_time_min">
<option value="0">...</option>
<?PHP
	for ($j=0; $j<=55; $j+=5){
	echo '<option value="'.$j.'">';
	if ($j==0) echo $j;
	if ($j==5) echo '0';
	echo $j.'</option>';
	}
?>
</select>min
<br /><br />
<b>Enable this meeting</b><br />
<select name="enable">
<option value="yes">yes</option>
<option value="no">no</option>
</select><br /><br />
<a href="./scheduler">cancel</a> <input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP } ?>