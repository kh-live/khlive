<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2><?PHP echo $lng['download'];?></h2>
<table>
<?PHP
echo '<tr><td><b>'.$lng['file'].'</b></td><td><b>'.$lng['size'].'</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
 if ($dh = @opendir("./downloads")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 
	   if (!strstr($file, ".mp4")) {
	   $info=filesize("./downloads/".$file);
	   if ($info>=1048576){
	   $info=round($info/1048576,1);
	   $info.=" MB";
	   }elseif($info>=1024){
	   $info=round($info/1024,1);
	   $info.=" kB";
	   }else{
	    $info.=" B";
	   }
                     echo'<tr><td>'.$file.'</td><td>'.$info.'</td><td><a href="./downloads/'.$file.'">'.$lng['download'].'</a></td></tr>';
}
                     }
		}
	closedir($dh);
	}
?>
</table>
<?PHP
if (isset($_FILES['monfichier'])){
 if ($_FILES['monfichier']['error']==0) {
    $name=basename($_REQUEST['name']);
$nomOrigine = $_FILES['monfichier']['name'];
if ($name=="") {
$name=basename($nomOrigine);
$fullname=$name;
}else{
$elementsChemin = pathinfo($nomOrigine);
$extensionFichier = $elementsChemin['extension'];
$fullname=$name.".".$extensionFichier;
}
if ( is_file("./downloads/".$fullname)){
echo "error : file name already used";
}else {    
    $repertoireDestination = "./downloads/";
    $nomDestination = $fullname;

    if (move_uploaded_file($_FILES["monfichier"]["tmp_name"], 
                                     $repertoireDestination.$nomDestination)) {
        echo "file added successfuly";
	}else{
	echo "error";
	}
 }
}
}
?>
<h3> Upload a file</h3>
 it will be uploaded in downloads folder
<form enctype="multipart/form-data" action="" method="post">
     <table>
     <tr><td> <input type="hidden" name="MAX_FILE_SIZE" value="102400000" />
      file</td><td> <input type="file" name="monfichier" /></td></tr>
    <tr><td>
      file's name (without ext - leave blank to keep the same name)</td><td> <input type="text" name="name" /></td></tr>
        </table>
        <input type="submit" value="Send" />
    </form>
</div>
