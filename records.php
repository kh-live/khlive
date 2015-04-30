<?PHP
$print='ok';

$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
$a = session_id();
if ($a == ''){
session_start();
}
include "db/config.php";
include "lang.php";

echo '<html><head>
<style type="text/css">
body {
    color: black;
    font-family: sans-serif;
    font-size: 16px;
    margin: 0;
    overflow-x: hidden;
}
#page {
    background-color: white;
    overflow-y: auto;
    padding: 10px 10px 30px;
}

#feeds {
    padding-left: 10px;
}
#feeds u {
    color: red;
    text-decoration: none;
}
#number_at {
    background-color: grey;
    color: white;
    font-size: 20px;
    margin-top: 20px;
    min-height: 30px;
    padding: 10px;
    text-align: center;
}
#sms_small {
    background-color: grey;
    color: white;
    font-size: 20px;
    margin-top: 20px;
    min-height: 30px;
    padding: 10px;
    text-align: center;
}
#sms {
    background-color: #eeeeee;
    border: 1px solid grey;
    display: none;
    margin-top: 30px;
    padding: 10px;
    width: 400px;
}
</style>
</head>
<body>';
}else{
//if it's from the master server, we need to redirect to the slave server
if ($server_beta=="master"){
$print='ko';
$url="";
$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$url=$data[1];
	}
	}
if ($url==""){
echo 'Could not find your congregations server...';
}else{

echo '<div id="page"><iframe id="listen_frame" src="http://'.$url.'/kh-live/records.php?user='.$_SESSION['user'].'"></iframe></div>';

}
}
}
if ($print=='ok'){
$selected_cong="";
$selected_date="";
// we need to set session type
if(isset($_GET['user'])){
$_SESSION['user']=$_GET['user'];
$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
        if (strtoupper($data[0])==strtoupper($_SESSION['user'])){
                $_SESSION['type']=$data[4];
		$_SESSION['cong']=$data[3];
}
}
}

if(isset($_GET['cong'])){
$selected_cong=$_GET['cong'];
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
  window.location="./record";
  }
 
}

function update_cong(url){
  window.location="./record?cong=" + url;
}

function update_date(url){
  window.location="./record?date=" + url;
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
	$date=substr($parts[1],0,6);
	$dates[$date]="ok";
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
	echo '<option value="'.$cong[0].'" '.$opt.'>'.$cong[0].'</option>';
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
	echo '</select><br /><br />Newest at the top';
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
                     echo'<tr><td>'.$file.'</td><td>'.$info.'</td><td><a href="./download.php?file='.$file.'" download>'.$lng['download'].'</a>';
if ($_SESSION['type']=="admin" OR $_SESSION['type']=="root"){
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
if (strstr($test, ".php")){
echo '</body></html>';
}
?>