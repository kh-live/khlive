<?PHP
$a = session_id();
if ($a == ''){
session_start();
}
 if (isset($_GET['file'])){
        $file_name=basename($_GET['file']);
$type='records';
if (isset($_GET['type'])){
if ($_GET['type']=='song') $type='kh-songs';
}
        if (file_exists("./".$type."/".$file_name)){
         if ($_SESSION['type']=="manager" OR $_SESSION['type']=="admin" OR $_SESSION['type']=="root") {
         $cong=$_SESSION['cong'];
         $client=$_SESSION['user'];
       if ($_SESSION['type']=="root" OR strstr($file_name, $cong)){
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
	// log that as it's an attempt at removing another congreg's file
	echo "access denied";
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
