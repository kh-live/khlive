<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['delete']){
		if ($_POST['server_confirmed']!=""){
			$server_confirmed=urldecode($_POST['server_confirmed']);//sanitize
			$db=file("db/servers");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$server_confirmed){
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/servers','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
	
			
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
	}
}

if(isset($_GET['server'])){
$server=$_GET['server']; //sanitize input
?>
<div id="page">
<h2>SERVERS</h2>
Remove server<br /><br />
<form action="./server_delete" method="post">
<b>server</b>: <?PHP echo $server;?><br />
<input type="hidden" name="server_confirmed" value="<?PHP echo $server;?>"><br />

<a href="./servers"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2>SERVERS</h2>
Click <a href="./servers">here</a> to edit more servers.<br /><br />
</div>
<?PHP
}
?>