<?PHP
$a = session_id();
if ($a == ''){
session_start();
}
 if (isset($_GET['file'])){
        $file_name=basename($_GET['file']);
$type='backup';

        if (file_exists("./".$type."/".$file_name)){
         if ($_SESSION['type']=="admin" OR $_SESSION['type']=="root") {
         $cong=$_SESSION['cong'];
         $client=$_SESSION['user'];

      if(unlink("./".$type."/".$file_name)){

        $info=time().'**info**delete '.$type.' file**'.$client.'**'.$cong."**".$file_name."**\n";
        $file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
                        if(fputs($file,$info)){
                        fclose($file);
                        }

        $info=time().'**info**delete '.$type.' file**'.$client."**".$file_name."**\n";
        $file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
                        if(fputs($file,$info)){
                        fclose($file);
                        }
        header('Location: '.$_SERVER['HTTP_REFERER']);
	}else{
	//log
        echo "error";
        }

 }else{
        include ("404.php");
        }
        }else{
        include ("404.php");
        }
	}else{
        include ("404.php");
        }
?>
