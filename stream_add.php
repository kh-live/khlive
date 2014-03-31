<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

$error="";
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['type']!="0" AND $_POST['congregation']!="0" AND $_POST['name']!=""){
			$cong=$_POST['congregation'];
			$stream_path="/stream-".$cong.".ogg";
			$db=file("db/streams");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$stream_path) $error="ko";
			}
			if ($error!="ko"){
			$info=$stream_path.'**'.$_POST['congregation'].'**'.$_POST['type'].'**'.$_POST['name']."**\n"; //sanitize input
			$file=fopen('./db/streams','a');
			if(fputs($file,$info)){
			fclose($file);
			$info2="<!--mount-".$cong."-->
<mount>
	<mount-name>".$stream_path."</mount-name>
	<username>source</username>
        <password>1234</password>
<authentication type=\"url\">
	<option name=\"mount_add\" value=\"http://localhost/kh-live/stream_start.php\"/>
        <option name=\"mount_remove\" value=\"http://localhost/kh-live/stream_end.php\"/>
	<option name=\"listener_add\" value=\"http://localhost/kh-live/listener_joined.php\"/>
        <option name=\"listener_remove\" value=\"http://localhost/kh-live/listener_left.php\"/>
	<option name=\"auth_header\" value=\"icecast-auth-user: 1\"/>
</authentication>
</mount>
<!--lastmount-->
";
	//change password
	$db=file("config/icecast.xml");
			$file_content="";
	foreach($db as $line){
		if (strstr($line,"<!--lastmount-->")){
		$file_content.=$info2;
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/icecast.xml','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'...</div>';
		}
	}
}
?>
<div id="page">
<h2><?PHP echo $lng['streams'];?></h2>
<?PHP echo $lng['add_new_stream'];?><br /><br />
<form action="./stream_add" method="post">
<b><?PHP echo $lng['name'];?></b><br />
<input class="field_login" type="text" name="name" ><br />
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<option value="'.$data[0].'">'.$data[0].'</option>';
	}
?>
</select><br />
<b><?PHP echo $lng['type'];?></b><br />
<select name="type">
<option value="ogg">ogg</option>
</select><br /><br />
<input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>">
</form>
</div>