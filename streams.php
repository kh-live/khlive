<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2><?PHP echo $lng['streams'];?></h2>
<a href="./stream_add"><?PHP echo $lng['add_new_stream'];?></a><br /><br />
<table>
<?PHP
echo '<tr><td><b>'.$lng['stream'].'</b></td><td><b>'.$lng['name'].'</b></td><td><b>'.$lng['congregation'].'</b></td><td><b>'.$lng['type'].'</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
$db=file("db/streams");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr><td>'.$data[0].'</td><td>'.$data[3].'</td><td>'.$data[1].'</td><td>'.$data[2].'</td>';
	echo '<td><a href="./stream_edit?stream='.urlencode($data[0]).'">'.$lng['edit'].'</a> - <a href="./stream_delete?stream='.urlencode($data[0]).'">'.$lng['delete'].'</a></td></tr>';
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