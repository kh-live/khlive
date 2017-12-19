<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if (!file_exists("db/timings")){
$info='Weekend**a:10:{s:5:"name1";s:21:"Opening Song & Prayer";s:7:"length1";s:1:"5";s:5:"name2";s:11:"Public Talk";s:7:"length2";s:2:"30";s:5:"name3";s:4:"Song";s:7:"length3";s:1:"5";s:5:"name4";s:16:"Watchtower Study";s:7:"length4";s:2:"60";s:5:"name5";s:24:"Concluding Song & Prayer";s:7:"length5";s:1:"5";}**'."\n".'Midweek**a:34:{s:5:"name1";s:21:"Opening song & prayer";s:7:"length1";s:1:"5";s:5:"name2";s:16:"Opening Comments";s:7:"length2";s:1:"3";s:5:"name3";s:32:"Treasures from God\'s Word - Talk";s:7:"length3";s:2:"10";s:5:"name4";s:26:"Digging for Spiritual Gems";s:7:"length4";s:1:"8";s:5:"name5";s:13:"Bible Reading";s:7:"length5";s:1:"4";s:5:"name6";s:7:"Counsel";s:7:"length6";s:1:"1";s:5:"name7";s:12:"Initial Call";s:7:"length7";s:1:"2";s:5:"name8";s:7:"Counsel";s:7:"length8";s:1:"1";s:5:"name9";s:12:"Return Visit";s:7:"length9";s:1:"4";s:6:"name10";s:7:"Counsel";s:8:"length10";s:1:"1";s:6:"name11";s:11:"Bible Study";s:8:"length11";s:1:"6";s:6:"name12";s:7:"Counsel";s:8:"length12";s:1:"1";s:6:"name13";s:4:"Song";s:8:"length13";s:1:"5";s:6:"name14";s:20:"Living as Christians";s:8:"length14";s:2:"15";s:6:"name15";s:24:"Congregation Bible Study";s:8:"length15";s:2:"30";s:6:"name16";s:16:"Review & Preview";s:8:"length16";s:1:"3";s:6:"name17";s:24:"Concluding song & prayer";s:8:"length17";s:1:"6";}**'."\n";
	$file=fopen('db/timings','w');
	fputs($file,$info);
	fclose($file);
}
if (isset($_GET['copy'])){
	if (is_numeric($_GET['copy'])){
		if (file_exists("db/timings")){
			$db=file("db/timings");
			$i=0;
			$copy='';
				foreach($db as $line){
				if ($i==$_GET['copy']) $copy=$line;
				$i++;
				}
			if ($copy!=''){
				$file=fopen('db/timings','a');
				fputs($file,$copy);
				fclose($file);
			}
		}
	}
}
?>
<div id="page">
<h2>Timing</h2>
This manages the meeting timings. You still need to link it to a scheduled meeting in the scheduler.<br /><br />
Click <a href="./time" target="_blank">HERE</a> to view the timer.<br /><br />
<?PHP
if ($_SESSION['type']=='root' OR $_SESSION['type']=='admin'){
?>
<a href="./timing_add">Add a new timing</a><br /><br />
<?PHP
}
?>
<table>
<?PHP
echo '<tr><td><b>ID</b></td><td><b>name</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
if (file_exists("db/timings")){
$db=file("db/timings");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr><td>'.$i.'</td><td>'.$data[0].'</td>';
	if ($_SESSION['type']=='root' OR $_SESSION['type']=='admin'){
	echo '<td><a href="./timing_edit?id='.$i.'">'.$lng['edit'].'</a> - <a href="./timing_delete?id='.$i.'">'.$lng['delete'].'</a> - <a href="./timings?copy='.$i.'">Duplicate</a></td>';
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