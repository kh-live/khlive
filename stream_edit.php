<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['name']!="" AND $_POST['stream_confirmed']!="" AND $_POST['congregation']!="0" AND $_POST['type']!="0" AND $_POST['stream_path']!="" AND strstr($_POST['stream_path'],"/") AND strpos($_POST['stream_path'],"/")=="0"){
			$stream_confirmed=urldecode($_POST['stream_confirmed']);
			$stream_new=$_POST['stream_path'];//sanitize input
			$congregation_new=$_POST['congregation'];//sanitize input
			$type_new=$_POST['type'];//sanitize input
			$name_new=$_POST['name'];//sanitize input
			$error="";
			if ($stream_new!=$stream_confirmed){
			$db=file("db/streams");
			foreach($db as $line){
			$data=explode ("**",$line);
			if ($data[0]==$stream_new) $error="ko";
			}
			}
			if ($error!="ko"){
			$db=file("db/streams");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$stream_confirmed){
		$file_content.=$stream_new.'**'.$congregation_new.'**'.$type_new.'**'.$name_new."**\n";
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/streams','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			}else{
			echo '<div id="error_msg">'.$lng['name_exists'].'...</div>';
			}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'</div>';
		}
	}
}
if(isset($_GET['stream'])){
$stream=urldecode($_GET['stream']); //sanitize input
$congregation="";
$db=file("db/streams");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$stream) {
	$congregation=$data[1];
	$type=$data[2];
	$name=$data[3];
	}
	}
?>
<div id="page">
<h2><?PHP echo $lng['streams'];?></h2>
<?PHP echo $lng['edit_stream'];?><br /><br />
<form action="./stream_edit" method="post">
<b><?PHP echo $lng['name'];?></b><br />
<input class="field_login" type="text" name="name" value="<?PHP echo $name;?>"><br />
<b><?PHP echo $lng['stream'];?></b><br />
<input class="field_login" type="text" name="stream_path" value="<?PHP echo $stream;?>"><br />
<b><?PHP echo $lng['congregation'];?></b><br />
<select name="congregation">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
$db=file("db/cong");
    foreach($db as $line){
    $selected="";
        $data=explode ("**",$line);
	if ($data[0]==$congregation) $selected="selected=selected";
	echo '<option value="'.$data[0].'" '.$selected.'>'.$data[0].'</option>';
	}
?>
</select><br />
<select name="type">
<option value="0"><?PHP echo $lng['select'];?>...</option>
<?PHP
$db=array("mp3","ogg");
    foreach($db as $line){
    $selected="";
	if ($line==$type) $selected="selected=selected";
	echo '<option value="'.$line.'" '.$selected.'>'.$line.'</option>';
	}
?>
</select><br /><br />
<input type="hidden" name="stream_confirmed" value="<?PHP echo $stream;?>">
<a href="./streams"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['streams'];?></h2>
Click <a href="./streams">here</a> to edit more streams.<br /><br />
</div>
<?PHP
}
?>