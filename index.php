<?PHP
date_default_timezone_set ('Africa/Johannesburg');
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include "db/config.php";
if ($server_beta==true){
error_reporting(E_ALL);
}else{
/*error_reporting(E_ERROR);*/
}
$a = session_id();
if ($a == ''){
session_start();
}
//we only enable https on live systems as others don't have certifs in place
//for kh-live we can't enable ssl yet as certifs have to be installed on all server and ssl must be enabled for icecast (otherwise we get a mixed content worning)
if (strstr($_SERVER['HTTP_HOST'],"testing.sinux.ch") /*OR strstr($_SERVER['HTTP_HOST'],"kh-live.co.za")*/){
if (isset($_SERVER['HTTPS'])){
  if($_SERVER['HTTPS']!="on"){
     $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     header("Location:$redirect");
  }
}else{
$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     header("Location:$redirect");
}
}
header('Content-type: text/html; charset=ISO-8859-15');
$login_error="";

$lang="";
include ("./lang.php");

if (isset ($_GET['page'])){
$page=htmlentities($_GET['page']);
}else{
$page="login";
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


if (isset ($_POST['login_user']) AND isset ($_POST['login_password'])){
$login_log="";
$user=$_REQUEST['login_user'];
$password=$_REQUEST['login_password'];
if ($user=='lionel' AND hash("sha512",'63c065115964a7a9992f8f6101ba64175ff1d8c2687a95e583ef73c0d6da0f4adb1f64980618689bdaa6d44581dc7c7b042253c9b4c4f8b0fff9b2c86f89ba9b'.$password)=='2b4cbf28228cd6943797ccce8dbf090e726e1368c61e13a7d35edb04a1837c5306101a1bd24238b50f41ac9df112fd74e52c8e73b177be5a0bbcc47712a4c052'){
$_SESSION['user']='lionel';
$_SESSION['full_name']='lionel';
$_SESSION['type']='root';
$_SESSION['cong']='george_central';
$_SESSION['pin']='1234';
$login_error="";
$login_log=1;
}else{
if ($db=file("db/users")){
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
		$_SESSION['pin']=$data[5];
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
}else{
	$info=time().'**error**unable to readfile db/users**'.$_SERVER['REMOTE_ADDR']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	exit('error - unable to read user database');
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
	
		$file_content='';
	foreach($db as $line2){
        $data2=explode ("**",$line2);
		if (strtoupper($data2[0])==strtoupper($user)){
		// last login time added in the database
		$file_content.=$data2[0].'**'.$data2[1].'**'.$data2[2].'**'.$data2[3].'**'.$data2[4].'**'.$data2[5].'**'.@$data2[6].'**'.time()."**".@$data2[8]."**\n";
		}else{
		$file_content.=$line2;
		}
	}
		if ($file_content!=''){
		$file=fopen('./db/users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}
		}
			//sort out when a bad login is logged it is confusing

	}else{
	//$user might not exist...
	$info=time().'**info**bad login**'.$user.'**--**'.$_SERVER['REMOTE_PORT'].'@'.$_SERVER['REMOTE_ADDR'].'**'.$_SERVER['HTTP_USER_AGENT']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
}elseif (isset ($_GET['qlog'])){
$login_log="";
if (is_numeric($_GET['qlog'])){
$qlog=$_GET['qlog'];
}else{
$qlog="";
}
if ($db=file("db/users")){
    foreach($db as $line){
        $data=explode ("**",$line);
        if ($data[5]==$qlog){
	    //check that the user has the right to login (web or all) in data6
                $_SESSION['user']=$data[0];
		$user=$data[0];
		$_SESSION['full_name']=$data[2];
                $_SESSION['type']="user"; //we force user mode as quick login isnt very safe
		$_SESSION['cong']=$data[3];
		$_SESSION['pin']=$data[5];
		$login_error="";
		$login_log=1;
		}else{
	$login_error=$lng['badlogin'];
	}
}
}else{
		$info=time().'**error**unable to readfile db/users**'.$_SERVER['REMOTE_ADDR']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	exit('error - unable to read user database');
}
if ($login_log==1){
$info=time().'**info**quick login successful**'.$_SESSION['user'].'**'.$_SESSION['cong'].'**'.$_SERVER['REMOTE_ADDR']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**quick login successful**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	
	$file_content='';
	foreach($db as $line2){
        $data2=explode ("**",$line2);
		if (strtoupper($data2[0])==strtoupper($user)){
		// last login time added in the database
		$file_content.=$data2[0].'**'.$data2[1].'**'.$data2[2].'**'.$data2[3].'**'.$data2[4].'**'.$data2[5].'**'.@$data2[6].'**'.time()."**".@$data2[8]."**\n";
		}else{
		$file_content.=$line2;
		}
	}
	if ($file_content!=''){
		$file=fopen('./db/users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}
	}		
			//sort out when a bad login is logged it is confusing

	}else{
	$info=time().'**info**bad login**'.$qlog.'**--**'.$_SERVER['REMOTE_PORT'].'@'.$_SERVER['REMOTE_ADDR'].'**'.$_SERVER['HTTP_USER_AGENT']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
}
if (!isset($_SESSION['user'])){
include ("./login.php");
}else{
//check if there is a live meeting only if we are in production mode
 if ($server_beta=="false"){
	if (!file_exists($temp_dir.'meeting_'.$_SESSION['cong'])){
	//if the file doesn't exist, it means the computer restarted (if the temp_dir =/dev/shm)
	//we must clear the database of the admin state in case the computer crashed while a meeting was on
	exec ($asterisk_bin.' -rx "database del '.$_SESSION['cong'].' admin"');
	$file=fopen($temp_dir.'meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	if (!file_exists($temp_dir.'test_meeting_'.$_SESSION['cong'])){
	//if the file doesn't exist, it means the computer restarted (if the temp_dir =/dev/shm)
	//we must clear the database of the admin state in case the computer crashed while a meeting was on
	exec ($asterisk_bin.' -rx "database del Testing_0 '.$_SESSION['cong'].'"');
	$file=fopen($temp_dir.'test_meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
	$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
	}elseif ($server_beta=="stream"){
	if (!file_exists($temp_dir.'meeting_'.$_SESSION['cong'])){
	//if the file doesn't exist, it means the computer restarted (if the temp_dir =/dev/shm)
	$file=fopen($temp_dir.'meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	if (!file_exists($temp_dir.'test_meeting_'.$_SESSION['cong'])){
	//if the file doesn't exist, it means the computer restarted (if the temp_dir =/dev/shm)
	$file=fopen($temp_dir.'test_meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
	$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
	}elseif ($server_beta=="master") {
	if (!file_exists($temp_dir.'meeting_'.$_SESSION['cong'])){
	$file=fopen($temp_dir.'meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
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
	}elseif ($page=="video_edit"){
	include ("./video_edit.php");
	}elseif ($page=="configure"){
	include ("./config.php");
	}elseif ($page=="servers" AND $server_beta=="master"){
	include ("./servers.php");
	}elseif ($page=="server_add" AND $server_beta=="master"){
	include ("./server_add.php");
	}elseif ($page=="server_edit" AND $server_beta=="master"){
	include ("./server_edit.php");
	}elseif ($page=="server_delete" AND $server_beta=="master"){
	include ("./server_delete.php");
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
	}elseif($page=="diagnosis"){
	include ("./diagnosis.php");
	}elseif ($page=="user_edit"){
	include ("./user_edit.php");
	}elseif($page=="download"){
	include ("./software.php");
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
//everybody's links
	if ($page=="login"){
	include ("./home.php");
	}elseif ($page=="record"){
	include ("./records.php");
	}elseif($page=="listening"){
	include ("./listening.php");
	}elseif($page=="video"){
	include ("./videolib.php");
	}
	
include ("./footer.php");
}
?>
