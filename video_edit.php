<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
			$url=urldecode($_POST['url']);
			$f_name=$_POST['f_name'];//sanitize input
			$name=$_POST['name'];//sanitize input
			$time=$_POST['time'];//sanitize input
			$cat=$_POST['cat'];//sanitize input
			$db=file("db/videos");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[1]==$f_name){
		$file_content.=$time.'**'.$f_name.'**'.$name.'**'.$cat.'**'.$url."**\n";
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/videos','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
	}
}
if(isset($_GET['file'])){
$stream=urldecode($_GET['file']); //sanitize input
$congregation="";
$db=file("db/videos");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[1]==$stream) {
	$time=$data[0];
	$name=$data[2];
	$cat=$data[3];
	$url=$data[4];
	}
	}
?>
<div id="page">
<h2>Videos</h2>
Edit<br /><br />
<form action="./video_edit" method="post">
<b>File name</b><br />
<input class="field_login" type="text" name="f_name" value="<?PHP echo $stream;?>" readonly="readonly"><br />
<b><?PHP echo $lng['name'];?></b><br />
<input class="field_login" type="text" name="name" value="<?PHP echo $name;?>"><br />
<b>Category</b><br />
<select name="cat" >
<option value="">...</option>
<option value="Annual Meeting"  <?PHP if($cat=='Annual Meeting') echo 'selected="selected"'; ?> >Annual Meeting</option>
<option value="Become Jehovahs friend" <?PHP if($cat=='Become Jehovahs friend') echo 'selected="selected"'; ?> >Become Jehovah's friend</option>
<option value="Bethel" <?PHP if($cat=='Bethel') echo 'selected="selected"'; ?> >Bethel</option>
<option value="Broadcasting" <?PHP if($cat=='Broadcasting') echo 'selected="selected"'; ?> >Broadcasting</option>
<option value="Construction" <?PHP if($cat=='Construction') echo 'selected="selected"'; ?> >Construction</option>
<option value="Conventions" <?PHP if($cat=='Conventions') echo 'selected="selected"'; ?> >Conventions</option>
<option value="Gilead" <?PHP if($cat=='Gilead') echo 'selected="selected"'; ?> >Gilead</option>
<option value="Interviews" <?PHP if($cat=='Interviews') echo 'selected="selected"'; ?> >Interviews</option>
<option value="Movies" <?PHP if($cat=='Movies') echo 'selected="selected"'; ?> >Movies</option>
<option value="Our Activities" <?PHP if($cat=='Our Activities') echo 'selected="selected"'; ?> >Our Activities</option>
<option value="Our Ministry" <?PHP if($cat=='Our Ministry') echo 'selected="selected"'; ?> >Our Ministry</option>
<option value="Relief Work" <?PHP if($cat=='Relief Work') echo 'selected="selected"'; ?> >Relief Work</option>
<option value="Sing with us" <?PHP if($cat=='Sing with us') echo 'selected="selected"'; ?> >Sing with us</option>
<option value="What Your Peers Say" <?PHP if($cat=='What Your Peers Say') echo 'selected="selected"'; ?> >What Your Peers Say</option>
<option value="Whiteboard Animations" <?PHP if($cat=='Whiteboard Animations') echo 'selected="selected"'; ?> >Whiteboard Animations</option>
</select><br />
<b>URL</b><br />
<input class="field_login" type="text" name="url" value="<?PHP echo $url;?>" readonly="readonly"><br />
<input type="hidden" name="time" value="<?PHP echo $time;?>">
<a href="./video"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['save'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Videos</h2>
Click <a href="./video">here</a> to edit more videos.<br /><br />
</div>
<?PHP
}
?>