<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if(!isset($_SESSION['selected_lang'])) $_SESSION['selected_lang']="";
if(!isset($_SESSION['selected_cat'])) $_SESSION['selected_cat']="";
if(!isset($_SESSION['selected_date_v'])) $_SESSION['selected_date_v']="new";

if(isset($_GET['lang'])){
$_SESSION['selected_lang']=$_GET['lang'];
}
if(isset($_GET['cat'])){
$_SESSION['selected_cat']=$_GET['cat'];
}
if(isset($_GET['date'])){
$_SESSION['selected_date_v']=$_GET['date'];
}
?>
<div id="page">
<h2>Video Library</h2>


<?PHP
if (isset($_POST['submit'])){
	if($_POST['submit']=="Add to list"){
$filename=$_POST['filename'];
$url=$_POST['url'];
$name=$_POST['name'];
$desc=$_POST['desc'];
$video_list=file("db/videos");
$test="";
	foreach($video_list as $line){
		$video=explode("**", $line);
		if (in_array($filename,$video)) $test="ok";
		}
if ($test=="ok"){
echo "error : file name already used";
}else {    
//we add the new file in the db
$info=time()."**".$filename."**".$name."**".$desc."**".$url."**\n";
$file=fopen('./db/videos','a');
			if(fputs($file,$info)){
			fclose($file);
			echo "link added successfully <br />";
			}
//then we generate the file list to download
$to_download=array();
$video_list=file("db/videos");
	foreach($video_list as $line){
		$video=explode("**", $line);
		if (!is_file("./downloads/".$video[1])){
			$to_download[$video[1]]=$video[4];
		}
	}
	$downloader_script="#!/bin/sh \n#downloader script \n\n";
	foreach($to_download as $filename => $url){
	$downloader_script.='FILE="/var/www/html/kh-live/downloads/'.$filename."\" \n".'URL="'.$url.$filename."\" \n".'if [ ! -f $FILE ]; then'." \n".'wget -q -O $FILE $URL'." \nfi \n";
	}
	$file=fopen('config/downloader.sh','w');
			if(fputs($file,$downloader_script)){
			fclose($file);
			echo "download list successfully generated<br />";
			}
	//we must still give exec rights to the file
	chmod('config/downloader.sh', 0755);
 }
}elseif ($_POST['submit']=="Add Links"){
$path=$_POST['path'];
$_SESSION['video_path']=$path;
}
}
?>
Language : <select onchange="video_change_lang(this.value)">
<option value="" <?PHP if($_SESSION['selected_lang']=='') echo 'selected="selected"'; ?> >...</option>
<option value="_E_" <?PHP if($_SESSION['selected_lang']=='_E_') echo 'selected="selected"'; ?> >English</option>
<option value="_AF_" <?PHP if($_SESSION['selected_lang']=='_AF_') echo 'selected="selected"'; ?> >Afrikaans</option>
</select>
Category : <select onchange="video_change_cat(this.value)">
<option value="" <?PHP if($_SESSION['selected_cat']=='') echo 'selected="selected"'; ?> >...</option>
<option value="Annual Meeting" <?PHP if($_SESSION['selected_cat']=='Annual Meeting') echo 'selected="selected"'; ?> >Annual Meeting</option>
<option value="Become Jehovahs friend" <?PHP if($_SESSION['selected_cat']=='Become Jehovahs friend') echo 'selected="selected"'; ?> >Become Jehovah's friend</option>
<option value="Bethel" <?PHP if($_SESSION['selected_cat']=='Bethel') echo 'selected="selected"'; ?> >Bethel</option>
<option value="Broadcasting" <?PHP if($_SESSION['selected_cat']=='Broadcasting') echo 'selected="selected"'; ?> >Broadcasting</option>
<option value="Construction" <?PHP if($_SESSION['selected_cat']=='Construction') echo 'selected="selected"'; ?> >Construction</option>
<option value="Conventions" <?PHP if($_SESSION['selected_cat']=='Conventions') echo 'selected="selected"'; ?> >Conventions</option>
<option value="Gilead" <?PHP if($_SESSION['selected_cat']=='Gilead') echo 'selected="selected"'; ?> >Gilead</option>
<option value="Interviews" <?PHP if($_SESSION['selected_cat']=='Interviews') echo 'selected="selected"'; ?> >Interviews</option>
<option value="Movies" <?PHP if($_SESSION['selected_cat']=='Movies') echo 'selected="selected"'; ?> >Movies</option>
<option value="Our Activities" <?PHP if($_SESSION['selected_cat']=='Our Activities') echo 'selected="selected"'; ?> >Our Activities</option>
<option value="Our Ministry" <?PHP if($_SESSION['selected_cat']=='Our Ministry') echo 'selected="selected"'; ?> >Our Ministry</option>
<option value="Relief Work" <?PHP if($_SESSION['selected_cat']=='Relief Work') echo 'selected="selected"'; ?> >Relief Work</option>
<option value="Sing with us" <?PHP if($_SESSION['selected_cat']=='Sing with us') echo 'selected="selected"'; ?> >Sing with us</option>
<option value="What Your Peers Say" <?PHP if($_SESSION['selected_cat']=='What Your Peers Say') echo 'selected="selected"'; ?> >What Your Peers Say</option>
<option value="Whiteboard Animations" <?PHP if($_SESSION['selected_cat']=='Whiteboard Animations') echo 'selected="selected"'; ?> >Whiteboard Animations</option>
</select>
Date : <select onchange="video_change_date(this.value)">
<option value="new" <?PHP if($_SESSION['selected_date_v']=='new') echo 'selected="selected"'; ?> >Newest first</option>
<option value="old" <?PHP if($_SESSION['selected_date_v']=='old') echo 'selected="selected"'; ?> >Oldest first</option>
</select>
<table>
<?PHP
$table_videos = array();
echo '<tr><td><b>'.$lng['file'].'</b></td><td><b>'.$lng['size'].'</b></td><td><b>'.$lng['status'].'</b></td></tr>';
//we must check if the file is on the video list
$video_list=file("db/videos");
	foreach($video_list as $line){
		$video=explode("**", $line);
		$status="";
	if (($_SESSION['selected_lang']=='' OR strstr($video[1],$_SESSION['selected_lang'])) AND ($_SESSION['selected_cat']=='' OR strstr($video[3],$_SESSION['selected_cat']))){
 if ($dh = @opendir("./downloads")) {
       while (($file = readdir($dh)) !== false) {
	if ($file==$video[1]){
	   $info=filesize("./downloads/".$file);
	   if ($info>=1073741824){
	   $info=round($info/1073741824,1);
	   $info.=" GB";
	   }elseif ($info>=1048576){
	   $info=round($info/1048576,1);
	   $info.=" MB";
	   }elseif($info>=1024){
	   $info=round($info/1024,1);
	   $info.=" kB";
	   }else{
	    $info.=" B";
	   }
	   $link="Available at KH";
	   if (($_SESSION['type']=="manager" OR $_SESSION['type']=="root") AND strstr($_SERVER['HTTP_HOST'], "192.168.1.123")) $link='<a href="./downloads/'.$file.'">'.$lng['download'].'</a>';
	   if ($_SESSION['type']=="root") $link.=' - <a href="./video_edit?file='.$file.'">Edit</a>';
	   if (isset($_SESSION['video_path'])) $link.=' - <a href="file:///'.$_SESSION['video_path'].$file.'">Local link</a>';
                     $table_videos[]='<tr><td>'.$video[2].'</td><td>'.$info.'</td><td>'.$link.'</td></tr>';
                     $status="ok";
		}
	 }
	closedir($dh);
	}
		if ($status==""){
		$table_videos[]='<tr><td>'.$video[2].'</td><td>-</td><td>Available tomorrow</td></tr>';
		}
	}
	}
if ($_SESSION['selected_date_v']=="new"){
$new_videos=array_reverse($table_videos);
foreach ($new_videos as $video){
echo $video;
}
}else{
foreach ($table_videos as $video){
echo $video;
}
}
?>
</table>
<?PHP
if ($_SESSION['type']=="root"){
?>
<h3> Add video to be downloaded</h3>
 it will be uploaded in downloads folder on the next day at 00:05am
<form action="" method="post">
     <table>
     <tr><td>
     filename path on jw.org</td><td> <input type="text" name="filename" /></td></tr>
     <tr><td>
     video path on jw.org</td><td> <input type="text" name="url" /></td></tr>
    <tr><td>
      video's title</td><td> <input type="text" name="name" /></td></tr>
      <tr><td>
     video's category</td><td><select name="desc" >
<option value="">...</option>
<option value="Annual Meeting">Annual Meeting</option>
<option value="Become Jehovahs friend">Become Jehovah's friend</option>
<option value="Bethel">Bethel</option>
<option value="Broadcasting">Broadcasting</option>
<option value="Construction">Construction</option>
<option value="Conventions">Conventions</option>
<option value="Gilead">Gilead</option>
<option value="Interviews">Interviews</option>
<option value="Movies">Movies</option>
<option value="Our Activities">Our Activities</option>
<option value="Our Ministry">Our Ministry</option>
<option value="Relief Work">Relief Work</option>
<option value="Sing with us">Sing with us</option>
<option value="What Your Peers Say">What Your Peers Say</option>
<option value="Whiteboard Animations">Whiteboard Animations</option>
</select></td></tr>
        </table>
        <input type="submit" name="submit" value="Add to list" />
    </form>
    <h3> Link to local drive</h3>
adds a link to open file from your computer<br />
then save the web page on your computer and open from local directory
<form action="" method="post">
     <table>
     <tr><td>
     Local directory (f. eg. "f:\jw_videos\) </td><td> <input type="text" name="path" /></td></tr>
        </table>
        <input type="submit" name="submit" value="Add Links" />
    </form>
<?PHP
}
?>
</div>
<script>
function video_change_lang(e){
 window.location="./video?lang=" + e;
}
function video_change_cat(e){
 window.location="./video?cat=" + e;
}
function video_change_date(e){
 window.location="./video?date=" + e;
}
</script>