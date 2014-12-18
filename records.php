<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$selected_cong="";
$selected_date="";
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
