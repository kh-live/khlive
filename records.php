<?PHP
$print='ok';

$test=$_SERVER['REQUEST_URI'];
$url_parts = parse_url($test);
if (strstr($test, ".php")){
$a = session_id();
if ($a == ''){
session_start();
if (strstr($_SERVER['HTTP_ORIGIN'], 'https')){
header('Access-Control-Allow-Origin: https://kh-live.co.za');
}else{
header('Access-Control-Allow-Origin: http://kh-live.co.za');
}
}
include "db/config.php";
include "lang.php";
}else{
//if it's from the master server, we need to redirect to the slave server
if ($server_beta=="master"){
$print='ko';
$url="";
$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$url=$data[0];
	$ip=$data[1];
	//we need to check if SSL is enabled and which port to use
	$s_enable_ssl=@$data[5];
	$s_http_port=@$data[6];
	$s_https_port=@$data[7];
	}
	}
if ($url==""){
echo 'Could not find your congregations server...';
}else{
	if ($s_enable_ssl=='force'){
		$url='https://'.$url.':'.$s_https_port;
	}elseif($s_enable_ssl=='no' OR $s_enable_ssl==''){
		if ($s_http_port!='') $s_http_port=':'.$s_http_port;
		$url='http://'.$ip.$s_http_port;
	}else{
	//this is auto
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']=="on"){
		$url='https://'.$url.':'.$s_https_port;
		}else{
		if ($s_http_port!='') $s_http_port=':'.$s_http_port;
		$url='http://'.$ip.$s_http_port;
		}
	}
echo '<div id="records_frame"><div id="page">
<h2>'.$lng['recordings'].'</h2>Connecting...</div></div>';
echo '
<script type="text/javascript">
function update_rec(url, user, cong, dateR){
	if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    resp=xmlhttp.responseText;
    document.getElementById(\'records_frame\').innerHTML=resp;
    }else{
    document.getElementById(\'records_frame\').innerHTML="<div id=\"page\"><h2>'.$lng['recordings'].'</h2>Loading, Please wait...</div>";
    }
  }
  tstmp = new Date();
xmlhttp.open("GET", url + "/kh-live/records.php?user=" + user + "&date=" + dateR + "&cong=" + cong + "&tmp=" +  tstmp.getTime() , true);
xmlhttp.send();
}
update_rec("'.$url.'","'.urlencode($_SESSION['user']).'","'.urlencode($_SESSION['cong']).'","" );

function update_cong(cong){
  update_rec("'.$url.'","'.urlencode($_SESSION['user']).'", cong,"" );
}

function update_date(dateR){
 update_rec("'.$url.'","'.urlencode($_SESSION['user']).'", "",dateR);
}
</script>';
}
}
}
if ($print=='ok'){
$selected_cong="";
$selected_date="";
if (isset($_POST['del_all'])){
	if (isset($_POST['del_month'])){
		if ($_POST['del_month']!=""){
		if (isset($_SESSION['type'])){
			if ($_SESSION['type']=='root') {
			delAllRecords($_POST['del_month'],"root");
			}elseif ($_SESSION['type']=='admin'){
			delAllRecords($_POST['del_month'],$_SESSION['cong']);
			}
		}
		}
	}
}
if(isset($_GET['user'])){
/*warning doing this bypasses login*/
//so we must destroy the session as soon as the ajax call has been done.
//or better we musn't use the session user in the script but a local variable
$destroy_session_at_the_end='ok';
$record_user=urldecode($_GET['user']);
$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
        if (strtoupper($data[0])==strtoupper($record_user)){
                //$_SESSION['type']=$data[4];
		//we must force the remote user type to user, otherwise, this allows a remote person to login as admin without a password
		$_SESSION['type']='user';
		$_SESSION['cong']=$data[3];
}
}
}

if(isset($_GET['cong'])){
$selected_cong=urldecode($_GET['cong']);
$_SESSION['selected_cong_r']=$selected_cong;
$_SESSION['selected_date_r']="";
}
if(isset($_GET['date'])){
$selected_date=$_GET['date'];
$_SESSION['selected_date_r']=$selected_date;
}
if(isset($_SESSION['selected_cong_r'])) $selected_cong=$_SESSION['selected_cong_r'];
if(isset($_SESSION['selected_date_r'])) $selected_date=$_SESSION['selected_date_r'];
?>
<script type="text/javascript">
function show_confirm(i)
{

var r=confirm("Are you sure you want to delete this recording :" + i +" ?");
if (r==true)
  {
  window.location="./delete.php?file=" + i ;
  }
else
  {
  window.location="<?PHP echo $url_parts['path'] ; ?>";
  }
 
}

function update_cong(url){
  window.location="<?PHP echo $url_parts['path'] ; ?>?cong=" + url;
}

function update_date(url){
  window.location="<?PHP echo $url_parts['path'] ; ?>?date=" + url;
}
</script>
<div id="page">
<h2><?PHP echo $lng['recordings'];?></h2>
<?PHP
$tmp_results=array();
 if ($dh = @opendir("./records")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 
	   if (strstr($file,$_SESSION['cong']) OR $_SESSION['type']=="root") {
		if ($selected_cong!=""){
			if(strstr($file,$selected_cong)) $tmp_results[]=$file;
		}else{
	   $tmp_results[]=$file;
		}
	   }
	   }
	   }
	   closedir($dh);
	}
	rsort($tmp_results);
	$dates=array();
	foreach ($tmp_results as $file){
	$parts=explode("-",$file);
	if (isset($parts[1])){
	$date=substr($parts[1],0,6);
	$dates[$date]="ok";
	}
	}
	ksort($dates);
	if ($_SESSION['type']=="root"){
	echo $lng['congregation'].': <select id="cong" onchange="javascript:update_cong(this.value)">
	<option value="">...</option>';
	$congs=file('./db/cong');
	foreach($congs as $congreg){
	$cong=explode("**",$congreg);
	$opt="";
	if ($selected_cong==$cong[0]) $opt='selected="selected"';
	echo '<option value="'.urlencode($cong[0]).'" '.$opt.'>'.$cong[0].'</option>';
	}
	echo '</select><br /><br />';
	}
	echo 'Date: <select id="date" onchange="javascript:update_date(this.value)">
	<option value="">...</option>';
	foreach($dates as $date=>$nothing){
	$year=substr($date,0,4);
	$month=substr($date,4,2);
	$opt="";
	if ($selected_date==$date) $opt='selected="selected"';
	echo '<option value="'.$date.'" '.$opt.'>'.$month.'/'.$year.'</option>';
	}
	echo '</select><br /><br />';
	if ($_SESSION['type']=='admin' OR $_SESSION['type']=='root'){
	echo '<form action="" method="post"><input type="hidden" value="'.$selected_date.'" name="del_month" /><input type="submit" name="del_all" value="delete all recordings for the selected month" /></form><br /><br />';
	}
	echo 'Newest at the top';
echo '<table><tr><td><b>'.$lng['file'].'</b></td><td><b>'.$lng['size'].'</b></td><td><b>'.$lng['actions'].'</b></td></tr>';

	foreach($tmp_results as $file)
	{
	   $info=filesize("./records/".$file);
		if ($info>=1048576){
	   $info=round($info/1048576,1);
	   $info.=" MB";
	   }elseif($info>=1024){
	   $info=round($info/1024,1);
	   $info.=" kB";
	   }else{
	    $info.=" B";
	   }
	   $go="ko";
	   if($selected_date!=""){
	   if(strstr($file,$selected_date)){
	   $go="ok";
	   }
	   }else{
	   $go="ok";
	   }
	   if($go=="ok"){
	   $tmptmp=explode('?',$_SERVER['REQUEST_URI']); 
                     echo'<tr><td>'.$file.'</td><td>'.$info.'</td><td><a href="//'.$_SERVER['HTTP_HOST'].str_replace('record', '', str_replace('records.php','',$tmptmp[0])).'download.php?file='.$file.'" >'.$lng['download'].'</a>';
if (($_SESSION['type']=="admin" OR $_SESSION['type']=="root") AND !strstr($test, ".php")){
echo '- <a href="javascript:show_confirm(\''.$file.'\')">'.$lng['delete'].'</a>';
}
echo '</td></tr>';
}
}		
	
?>
</table>
</div>
<?PHP
}
function delAllRecords($month,$cong){
$deleted='';
if ($month!="" AND $cong!=""){
if ($dh = @opendir("./records")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 
	   if (strstr($file,$cong) OR $_SESSION['type']=="root") {
		if (strstr($file, $month)){
			$tmp_name=explode('-',$file);
			$tmp_name2=explode('_',$tmp_name[1]);
			$tmp_name3=substr($tmp_name2[0],0,6);
			if ($tmp_name3==$month){
				unlink('./records/'.$file);
				$deleted='ok';
			}
		}
	   }
	   }
	   }
	   closedir($dh);
	}
}
if ($deleted!=''){
	$info=time().'**info**multi records deleted - '.$month.' - '.$cong.'**'.$_SESSION['user'].'**'.$_SESSION['cong']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
}
}
if (isset($destroy_session_at_the_end)){
//if we call this script through ajax, we must destroy the session to prevent the remote user from connecting to the local server without the password
    $_SESSION = array();
    session_destroy();
    session_start();
}
?>