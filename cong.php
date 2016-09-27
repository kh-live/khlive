<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
<a href="./cong_add"><?PHP echo $lng['add_new_congregation'];?></a><br /><br />
<table>
<?PHP
echo '<tr><td><b>'.$lng['name'].'</b></td><td><b>'.$lng['cong_id'].'</b></td><td><b>'.$lng['admin_pin'].'</b></td><td><b>phone no</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr><td>'.$data[0].'</td><td>'.@$data[1].'</td><td>'.@$data[2].'#</td><td>'.@$data[4].'</td>';
	echo '<td><a href="./cong_edit?cong='.urlencode($data[0]).'">'.$lng['edit'].'</a> - <a href="./cong_delete?cong='.urlencode($data[0]).'">'.$lng['delete'].'</a></td></tr>';
	//add edit function
	}
	
	?>
	</table>
	<h2><?PHP echo $lng['streams'];?></h2>
<table>
<?PHP
echo '<tr><td><b>'.$lng['stream'].'</b></td><td><b>'.$lng['congregation'].'</b></td><td><b>'.$lng['type'].'</b></td></tr>';
$db=file("db/streams");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr><td>'.$data[0].'</td><td>'.$data[1].'</td><td>'.$data[2].'</td></tr>';
	}
	
	?>
</table>
<h2><?PHP echo $lng['live_streams'];?></h2>
<?PHP echo $lng['live_streams_txt'];?><br /><br />
<table>
<?PHP
echo '<tr><td><b>'.$lng['stream'].'</b></td><td><b>'.$lng['date_started'].'</b></td></tr>';
$db=file("db/live_streams");
	if (count($db)==0){
	echo '<tr><td>'.$lng['no_live_streams'].'</td><td></td>';
	}else{
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr><td>'.$data[0].'</td><td>'.date('H:i:s-d/m/Y',$data[1]).'</td>';
	}
	}
	?>
</table>
</div>