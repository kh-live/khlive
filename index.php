<?PHP
date_default_timezone_set ('Africa/Johannesburg');
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if (strstr($_SERVER['HTTP_HOST'], "192.168.1.123")){
error_reporting(E_ALL);
}else{
/*error_reporting(E_ERROR);*/
}
$a = session_id();
if ($a == ''){
session_start();
}

if (isset($_SERVER['HTTPS'])){
  if($_SERVER['HTTPS']!="on" AND $_SERVER['HTTP_HOST']!="192.168.1.123" )
  {
     $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     header("Location:$redirect");
  }
}else{
if($_SERVER['HTTP_HOST']!="192.168.1.123" AND $_SERVER['HTTP_HOST']!="localhost:8080")
  {
$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     header("Location:$redirect");
     }
}

header('Content-type: text/html; charset=ISO-8859-15');

$login_error="";
$site_css_default="default.css";
$site_css="style.css";
$site_css_mobile="mobile.css";
//adress to test if the server is live - localhost
$server="localhost";
//adress to generate links for streams
$server_out="khlive.mooo.com";
$port="8000";
$lang="";
$timer="60";
$version="0.99";

include ("./lang.php");

if (isset ($_REQUEST['page'])){
$page=htmlentities($_REQUEST['page']);
}else{
$page="login.php";
}
if ($page=="logout"){

if(isset($_SESSION['user']) AND isset($_SESSION['cong'])){
$info=time().'**info**logout successful**'.$_SESSION['user'].'**'.$_SESSION['cong'].'**'.$_SERVER['REMOTE_ADDR']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**logout successful**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
    $_SESSION = array();
    session_destroy();
    session_start();
    }


if (isset ($_REQUEST['login_user']) AND isset ($_REQUEST['login_password'])){
$login_log="";
$user=$_REQUEST['login_user'];
$password=$_REQUEST['login_password'];
$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
        if (strtoupper($data[0])==strtoupper($user)){
		$hashsplit=explode('--',$data[1]);
	$salt=$hashsplit[0];
	$hash=$hashsplit[1];
            if (hash("sha512",$salt.$password) == $hash){
	    //check that the user has the right to login (web or all) in data6
	    //check that the password is 8 char at least
                $_SESSION['user']=$data[0];
		$_SESSION['full_name']=$data[2];
                $_SESSION['type']=$data[4];
		$_SESSION['cong']=$data[3];
		$login_error="";
		$login_log=1;
		}else{
		$login_error=$lng['badlogin'];
		}
		}else{
	$login_error=$lng['badlogin'];
	//this is true only when the username is typed wrong
	}
}

if ($login_log==1){
$info=time().'**info**login successful**'.$_SESSION['user'].'**'.$_SESSION['cong'].'**'.$_SERVER['REMOTE_ADDR']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**login successful**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	
		$db2=file("db/users");
			$file_content="";
	foreach($db2 as $line2){
        $data2=explode ("**",$line2);
		if (strtoupper($data2[0])==strtoupper($user)){
		
		$file_content.=$data2[0].'**'.$data2[1].'**'.$data2[2].'**'.$data2[3].'**'.$data2[4].'**'.$data2[5].'**'.@$data2[6].'**'.time()."**\n";
		}else{
		$file_content.=$line2;
		}

		$file=fopen('./db/users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}
			}
			//sort out when a bad login is logged it is confusing

	}else{
	$info=time().'**info**bad login**'.$user.'**--**'.$_SERVER['REMOTE_PORT'].'@'.$_SERVER['REMOTE_ADDR'].'**'.$_SERVER['HTTP_USER_AGENT']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
}
if (!isset($_SESSION['user'])){
include ("./login.php");
}else{
//check if there is a live meeting
	if (!file_exists('/dev/shm/meeting_'.$_SESSION['cong']) AND $_SERVER['HTTP_HOST']!="localhost:8080"){
	$file=fopen('/dev/shm/meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	if (!file_exists('/dev/shm/test_meeting_'.$_SESSION['cong']) AND $_SERVER['HTTP_HOST']!="localhost:8080"){
	$file=fopen('/dev/shm/test_meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	if ($_SERVER['HTTP_HOST']!="localhost:8080"){
	$_SESSION['meeting_status']=implode("",file('/dev/shm/meeting_'.$_SESSION['cong']));
	$_SESSION['test_meeting_status']=implode("",file('/dev/shm/test_meeting_'.$_SESSION['cong']));
	}else{
	$_SESSION['meeting_status']="down";
	$_SESSION['test_meeting_status']="down";
	}
			
include ("./header.php");
//root links only
if ($_SESSION['type']=="root"){
	if ($page=="congregations"){
	include ("./cong.php");
	}elseif ($page=="cong_edit"){
	include ("./cong_edit.php");
	}elseif ($page=="cong_add"){
	include ("./cong_add.php");
	}elseif ($page=="cong_delete"){
	include ("./cong_delete.php");
	}elseif ($page=="logs"){
	include ("./logs.php");
	}elseif($page=="download"){
	include ("./software.php");
	}elseif($page=="diagnosis"){
	include ("./diagnosis.php");
	}
	}
//admin links only
if ($_SESSION['type']=="admin" OR $_SESSION['type']=="root"){
	if ($page=="users"){
	include ("./users.php");
	}elseif ($page=="user_add"){
	include ("./user_add.php");
	}elseif ($page=="user_delete"){
	include ("./user_delete.php");
	}elseif ($page=="user_edit"){
	include ("./user_edit.php");
	}
	}
//manger links only
	if($_SESSION['type']=="admin" OR $_SESSION['type']=="manager" OR $_SESSION['type']=="root"){
	
	if ($page=="meeting"){
	include ("./meeting.php");
	}elseif($page=="report"){
	include ("./report.php");
	}
	}
//homepage error message if server isnt live
//but it doesnt help right now as the server has to be live to see this page...
/*if ($_SESSION['server_status']=="ok"){*/
	if ($page=="login"){
	include ("./home.php");
	}
/*}else{
	if ($page=="login"){
	include ("./error.php");
	}
}*/
//all other links
if ($page=="record"){
	include ("./records.php");
	}elseif($page=="listening"){
	include ("./listening.php");
	}
	
include ("./footer.php");
}
?>
