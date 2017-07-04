<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2>Scheduler</h2>
Please note that no checks are done to make sure the schedule is consistent. Make sure the stop time is after the start time and that no meeting from an other congregation overlaps.<br /><br />
<b>IMPORTANT : The scheduler is in beta. Use with caution. Dont rely on it. Monitor the meeting page.</b><br /><br />
<a href="./sched_add">Add a new schedulded meeting</a><br /><br />
<table>
<?PHP
echo '<tr><td><b>ID</b></td><td><b>Congregation</b></td><td><b>Day</b></td><td><b>Time start</b></td><td><b>Time stop</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
if (file_exists("db/sched")){
$db=file("db/sched");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr';
	if (@$data[4]!='yes') echo ' style="color:grey;" ';
	$tmp=explode(':',$data[2]);
	$tmp1=explode(':',$data[3]);
	echo'><td>'.$i.'</td><td>'.$data[0].'</td><td>'.@$data[1].'</td><td>'.sprintf('%02d:%02d', $tmp[0],$tmp[1]).'</td><td>'.sprintf('%02d:%02d', $tmp1[0],$tmp1[1]).'</td>';
	if ($_SESSION['type']=='root' OR $_SESSION['cong']==$data[0]){
	echo '<td><a href="./sched_edit?id='.$i.'">'.$lng['edit'].'</a> - <a href="./sched_delete?id='.$i.'">'.$lng['delete'].'</a></td>';
	}else{
	echo '<td>--</td>';
	}
	echo '</tr>';
	//hide schedulded meetings that don't belong to the cong for admin and manager
	$i++;
	}
	}
	?>
	</table>
</div>