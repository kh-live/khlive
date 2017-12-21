<?PHP
$a = session_id();
if ($a == ''){
session_start();
}
 if (isset($_GET['file'])){
	$file_name=basename($_GET['file']);
	if (file_exists("./backup/".$file_name)){
	$tmptmp=explode('-', $file_name);
	 $cong=$tmptmp[0];
	 $client=@$_SESSION['user'];
	 $info=time().'**info**new backup download**'.$client.'**'.$cong."**".$file_name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**new backup download**'.$client."**".$file_name."**\n";
	$file=fopen('./db/logs-'.$cong.'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Location: ./backup/'.$file_name);
			exit();
 }else{
	include ("404.php");
	}
	}
?>