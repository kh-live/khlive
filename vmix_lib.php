<?PHP
$a = session_id();
if ($a == ''){
session_start();
}
include 'db/config.php';
if (isset($_GET['id']) AND isset($_GET['file']) AND isset($_SESSION['cong'])){
	if (is_numeric($_GET['id'])){
		if ($_GET['id']<=20 AND $_GET['id']>=1){
			$file=fopen($temp_dir.'vmix_'.$_GET['id'].'_'.$_SESSION['cong'],'w');
			fputs($file,$_GET['file']);
			fclose($file);
			echo 'saved';
		}
	}
}elseif (isset($_GET['id']) AND isset($_SESSION['cong'])){
	if (is_numeric($_GET['id'])){
		if ($_GET['id']<=20  AND $_GET['id']>=1){
			if (is_file($temp_dir.'vmix_'.$_GET['id'].'_'.$_SESSION['cong'])){
			echo file_get_contents($temp_dir.'vmix_'.$_GET['id'].'_'.$_SESSION['cong']);
			}
		}
	}
}elseif(isset($_GET['del']) AND isset($_SESSION['cong'])){
	if (is_numeric($_GET['del'])){
		if ($_GET['del']<=20  AND $_GET['del']>=1){
			if (is_file($temp_dir.'vmix_'.$_GET['del'].'_'.$_SESSION['cong'])){
			unlink($temp_dir.'vmix_'.$_GET['del'].'_'.$_SESSION['cong']);
			echo 'removed';
			}
		}
	}
}
?>