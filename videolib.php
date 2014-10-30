<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2>Video Library</h2>


<?PHP
if (isset($_POST['submit'])){
$filename=$_POST['filename'];
$url=$_POST['url'];
$name=$_POST['name'];
$desc=$_POST['desc'];
if ( is_file("./downloads/".$filename)){
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
}
?>
<table>
<?PHP
echo '<tr><td><b>'.$lng['file'].'</b></td><td><b>'.$lng['size'].'</b></td><td><b>'.$lng['status'].'</b></td></tr>';
//we must check if the file is on the video list
$video_list=file("db/videos");
	foreach($video_list as $line){
		$video=explode("**", $line);
		$status="";
	
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
                     echo'<tr><td>'.$file.'</td><td>'.$info.'</td><td><a href="./downloads/'.$file.'">'.$lng['download'].'</a></td></tr>';
                     $status="ok";
		}
	 }
	closedir($dh);
	}
		if ($status==""){
		echo'<tr><td>'.$video[1].'</td><td>-</td><td>Available tomorrow</td></tr>';
		}
	}
?>
</table>
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
     video's description</td><td> <input type="text" name="desc" /></td></tr>
        </table>
        <input type="submit" name="submit" value="Add to list" />
    </form>
</div>
