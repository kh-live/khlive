<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2>Notice Board</h2>
This allows you to display customized information on your users' homepage.<br /> Please don't post confidential information.
<br /><br />
<a href="./info_add">Add new information</a><br /><br />
<table>
<?PHP
echo '<tr><td><b>ID</b></td><td><b>Congregation</b></td><td><b>Info</b></td><td><b>link</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
if (file_exists("db/infos")){
$db=file("db/infos");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr';
	if (@$data[3]!='yes') echo ' style="color:grey;" ';
	echo '><td>'.$i.'</td><td>'.$data[0].'</td><td>'.@$data[1].'</td><td>'.@$data[2].'</td>';
	if ($_SESSION['type']=='root' OR $_SESSION['cong']==$data[0]){
	echo '<td><a href="./info_edit?id='.$i.'">'.$lng['edit'].'</a> - <a href="./info_delete?id='.$i.'">'.$lng['delete'].'</a></td>';
	}else{
	echo '<td>--</td>';
	}
	echo '</tr>';
	$i++;
	}
	}
	?>
	</table>
</div>
