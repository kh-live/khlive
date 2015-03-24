<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['server_name']!=""){
		$error="";
			$db=file("db/servers");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$_POST['server_name']) $error="ko";
			}
			if ($error==""){
			$server_name=$_POST['server_name']; //check
			$server_url=$_POST['server_url'];
			$server_api=$_POST['server_api'];
			$congs=$_POST['congs'];
			
			
			$info=$server_name."**".$server_url."**".$server_api."**".$congs."**\n";

			$file=fopen('./db/servers','a');
			if(fputs($file,$info)){
			fclose($file);
			echo 'success!';
			}
}else{
echo 'error : server already exists';
}
}
}
?>
	<div id="page">
<h2>SERVERS</h2>
Click <a href="./servers">here</a> to edit more servers.<br /><br />
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Servers</h2>
Add anew server<br /><br />
<form action="./server_add" method="post">
<b>server name</b><br />
<input class="field_login" type="text" name="server_name" />
<br /><br />
<b>server url</b><br />
<input class="field_login" type="text" name="server_url" />
<br /><br />
<b>server api</b><br />
<input class="field_login" type="text" name="server_api" />
<br /><br />
<b>congregations linked to server (separate with ";")</b><br />
<input class="field_login" type="text" name="congs" />
<br /><br />
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP } ?>