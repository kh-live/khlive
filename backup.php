<?PHP
$log='';
$test=$_SERVER['REQUEST_URI'];
$url_parts = parse_url($test);
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if (isset($_POST['submit'])){
	if ($_POST['submit']=='Upload'){
if (isset($_FILES['monfichier'])){
 if ($_FILES['monfichier']['error']==0) {
$nomOrigine = $_FILES['monfichier']['name'];
$ext = pathinfo($nomOrigine, PATHINFO_EXTENSION);
if ($ext!='zip'){
$log.= '<b style="color:red;">error : file must be a zip archive</b>';
}else{
$fullname=basename($nomOrigine);

if ( is_file("./backup/".$fullname)){
$log.= '<b style="color:red;">error : file name already used</b>';
}else {    
    $repertoireDestination = "./backup/";

    if (move_uploaded_file($_FILES["monfichier"]["tmp_name"], 
                                     $repertoireDestination.$fullname)) {
        $log.= '<b style="color:green;">file added successfuly</b>';
	}else{
	$log.= '<b style="color:red;">error while uploading file</b>';
	}
 }
}
}
}
}elseif ($_POST['submit']=='Create Now'){

if (!is_dir($temp_dir."kh-backup")){
if (!mkdir($temp_dir."kh-backup")){
$log.= '<b style="color:red;">error creating temporary folder</b>';
}
}
$files_to_backup=array(
'./db/config.php',
'./db/users',
'./db/cong',
'./db/infos',
'./db/sched',
'./db/streams',
'./db/timings'
);
foreach($files_to_backup as $file){
if (file_exists($file)){
copy($file,$temp_dir."kh-backup/".basename($file));
}
}

$rootPath = realpath($temp_dir."kh-backup");

// Initialize archive object
$zip = new ZipArchive();
$zip->open('./backup/kh-backup-'.date("Y",time()).'-'.date("m",time()).'-'.date("d",time()).'-'.date("His",time()).'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();

foreach($files_to_backup as $file){
if (file_exists($file)){
unlink($temp_dir."kh-backup/".basename($file));
}
}
    rmdir($temp_dir."kh-backup");

}
}

if (isset($_GET['restore_file'])){
	if (is_file('./backup/'.basename($_GET['restore_file']))){
		if (!is_dir($temp_dir."kh-backup")){
			if (!mkdir($temp_dir."kh-backup")){
				$log.= '<b style="color:red;">error creating temporary folder</b>';
			}
		}
		
	$zip = new ZipArchive();
$res = $zip->open('./backup/'.basename($_GET['restore_file']));
if ($res === TRUE) {
  $zip->extractTo($temp_dir."kh-backup");
  $zip->close();

} else {
$log.= '<b style="color:red;">error loading zip archive! Make sure it is valid.</b>';
}
if ($dh = @opendir($temp_dir."kh-backup")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')){ 
		if (!is_dir($file)){
		copy($temp_dir."kh-backup/".$file, "./db/".$file);
		}
	   }
	   }
	   }
if ($dh = @opendir($temp_dir."kh-backup")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')){ 
		if (!is_dir($file)){
		unlink($temp_dir."kh-backup/".$file);
		}
	   }
	   }
	   }

    rmdir($temp_dir."kh-backup");

        $info=time().'**info**backup restored from file**'.$_SESSION['user'].'**'.$_SESSION['cong']."**".basename($_GET['restore_file'])."**\n";
        $file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
                        if(fputs($file,$info)){
                        fclose($file);
                        }
	$log.= '<b style="color:green;">Backup restored successfuly! Now <a href="./configure?action=ok">CLICK HERE</a> to apply the restored backup configuration.<br /></b>';
    }
}
?>
<script type="text/javascript">
function show_confirm(i)
{

var r=confirm("Are you sure you want to delete this backup :" + i +" ?");
if (r==true)
  {
  window.location="./backup_delete.php?file=" + i ;
  }
else
  {
  window.location="<?PHP echo $url_parts['path'] ; ?>";
  }
 
}
function restore_confirm(i)
{

var r=confirm("Are you sure you want to restore this backup :" + i +" ?");
if (r==true)
  {
  window.location="./back_up?restore_file=" + i ;
  }
else
  {
  window.location="<?PHP echo $url_parts['path'] ; ?>";
  }
 
}
</script>
<div id="page">
<h2>Backup / Restore</h2>
<div><?PHP echo $log; ?><br /><b>Please note !</b> Only the following items will be saved/restored :<br />main config file / users db / congregations db / notice board db / timing db / scheduler db / streams db<br /><br /></div>
<table style="width:100%;">
<?PHP
echo '<tr><td><b>'.$lng['file'].'</b></td><td><b>'.$lng['size'].'</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
 if (!is_dir("./backup")) mkdir("./backup");
 if ($dh = @opendir("./backup")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 

	   $info=filesize("./backup/".$file);
	   if ($info>=1048576){
	   $info=round($info/1048576,1);
	   $info.=" MB";
	   }elseif($info>=1024){
	   $info=round($info/1024,1);
	   $info.=" kB";
	   }else{
	    $info.=" B";
	   }
                     echo'<tr><td>'.$file.'</td><td>'.$info.'</td><td><a href="./backup_download.php?file='.$file.'" >'.$lng['download'].'</a>';
if (($_SESSION['type']=="admin" OR $_SESSION['type']=="root") AND !strstr($test, ".php")){
echo '- <a href="javascript:show_confirm(\''.$file.'\')">'.$lng['delete'].'</a> - <a href="javascript:restore_confirm(\''.$file.'\')">Restore Backup</a>';
}
echo '</td></tr>';

                     }
		}
	closedir($dh);
	}
?>
</table>

<h3>Upload a backup</h3>
 it will be uploaded in backup folder
<form enctype="multipart/form-data" action="" method="post">
     <table>
     <tr><td> <input type="hidden" name="MAX_FILE_SIZE" value="102400000" />
      backup file</td><td> <input type="file" name="monfichier" /></td></tr>
        </table>
        <input name="submit" type="submit" value="Upload" />
    </form>
    <h3>Create a backup</h3>
 it will be created in backup folder
<form action="" method="post">
        <input name="submit" type="submit" value="Create Now" />
</form>

</div>
