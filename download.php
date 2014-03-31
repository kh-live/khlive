<?PHP
$a = session_id();
if ($a == ''){
session_start();
}
 if (isset($_GET['file'])){
	$file_name=basename($_GET['file']);
	if (file_exists("./records/".$file_name)){
	 if (strstr($file_name,$_SESSION['cong']) OR $_SESSION['type']=="root") {
	 $cong=$_SESSION['cong'];
	 $client=$_SESSION['user'];
	 $info=time().'**info**new download**'.$client.'**'.$cong."**".$file_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**new download**'.$client."**".$file_name."**\n";
	$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Location: ./records/'.$file_name);
			exit();
			//the following blogs any other connection until the script is finished
	/*header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$file_name);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize("./records/".$file_name));
ob_clean();
flush();
readfile("./records/".$file_name);*/


	
			
	}else{
	include ("404.php");
	}
 }else{
	include ("404.php");
	}
	}
?>
