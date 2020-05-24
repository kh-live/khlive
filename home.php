<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if ($_SESSION['type']=="root") {
if (isset($_GET['tempcong'])){
$_SESSION['cong']=urldecode($_GET['tempcong']);
}
if (isset($_GET['temptype'])){
$_SESSION['type']=urldecode($_GET['temptype']);
}
}
include ('functions.php');
?>
<div id="homepage">
<h2><?PHP echo $lng['home'];?></h2>
<?PHP
echo '<div class="home_widget"><b>'.$lng['welcome'].' '.$_SESSION['full_name'].'!</b><br />';
echo $lng['cong_part'].' : <b>'.$_SESSION['cong'].'</b>.<br />';
if ($_SESSION['type']=="root") {
echo 'You can change your cong for this session using the drop down menu : <br />';
?>
<script type="text/javascript">
function changeCong(){
var a = document.getElementById("tempcong").value;
if (a!=0)
window.location="./?tempcong=" + a ;
}
</script>
<?PHP
echo '<select id="tempcong" name="congregation" onchange="javascript:changeCong(this)" >
<option value="0">'.$lng['select'].'...</option>';

$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<option value="'.$data[0].'">'.$data[0].'</option>';
	}
echo '</select></br>';
echo 'You can change your type for this session using the drop down menu : <br />';
?>
<script type="text/javascript">
function changeType(){
var a = document.getElementById("temptype").value;
if (a!=0)
window.location="./?temptype=" + a ;
}
</script>
<?PHP
echo '<select id="temptype" name="types" onchange="javascript:changeType(this)" >
<option value="0">'.$lng['select'].'...</option>
<option value="user">user</option>
<option value="manager">manager</option>
<option value="admin">admin</option>';

echo '</select></br>';
}
if ($_SESSION['type']!="manager") echo 'Your quick login PIN is : <b>'.$_SESSION['pin'].'#</b></br>';

echo '</div>';
if ($server_beta=='master' AND $_SESSION['type']!="user"){
$ip='';
$url='';
$db1=file("db/servers");
    foreach($db1 as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$ip=$data[1];
	$url=$data[0];
	}
	}
if ($ip!='' AND $url!=''){
echo '<div class="home_widget"><b>Your local server</b><br />Here is the link to your local server (at the kingdom hall) for administration purposes : <br />
<a href="http://'.$url.'">'.$url.'</a> ( <a href="http://'.$ip.'">'.$ip.'</a> )</div>';
}
}
if ($_SESSION['type']=="user" OR $_SESSION['type']=="multi"){
echo '<div class="home_widget"><b>Hint :</b><br />Click on the top left &#9776; button to view the menu.<br /><br /><a target="_blank" href="http://wiki.kh-live.co.za/doku/doku.php?id=user_guide">Click here to view the user guide...</a></div>';
}else{
if ($server_beta!='master'){
$server_version=kh_fgetc_timeout('http://kh-live.co.za/version.php',2);
	if ($server_version!==false){
	if ($version!=$server_version){
	echo '<div class="home_widget"><b style="color:red;">Update available!</b><br />Your server is running an old version of kh-live software.';
	if ($_SESSION['type']=='root' OR $_SESSION['type']=='admin'){
	echo '<br /><a href="./auto_update">click here to update the server</a>';
	}else{
	echo 'Contact your administrator and ask him to update the server.';
	}
	echo '<br /> Current version : '.$version.' - New version : '.$server_version.'<br /><a target="_blank" href="http://wiki.kh-live.co.za/doku/doku.php?id=what_s_new">Click here to see what\'s new...</a></div>';
	}
	}else{
	echo '<div class="home_widget"><b style="color:red;">WARNING!</b><br />Your server\'s internet connection seems to be down. Ask your administrator to fix this!</div>';
	}
}
}
if ($_SESSION['meeting_status']=='live'){
echo '<a class="home_widget_link" href="./listening"><b>Scheduled meetings :</b><br /> There is a <b>live meeting</b> right now! <b><br /><br />Click here to listen in...</b></a>';
}elseif ($_SESSION['test_meeting_status']=='live'){
echo '<a class="home_widget_link" href="./listening"><b>Scheduled meetings :</b><br /> There is a <b style="color:red;">test meeting</b> right now! <b>Click here to listen in...</b></a>';
}
if ($server_beta!='master'){
if ($scheduler=='yes'){
echo '<div class="home_widget"><b>Scheduled meetings :</b><br />';
if (file_exists("db/sched")){
$db=file("db/sched");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	$tmp=explode(':',$data[2]);
	$tmp1=explode(':',$data[3]);
	
	if (($_SESSION['cong']==$data[0] OR $_SESSION['type']!='user')  AND $data[4]=='yes'){
	echo 'Meeting for '.$data[0].' : '.@$data[1].' ('.sprintf('%02d:%02d', $tmp[0],$tmp[1]).' - '.sprintf('%02d:%02d', $tmp1[0],$tmp1[1]).')<br />';
	}
	$i++;
	}
	}
echo '</div>';
}
}else{
$db=array();
$db1=file("db/servers");
    foreach($db1 as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$url=$data[1];
	}
	}
if ($url!=""){
$remote_db=kh_fgetc_timeout('http://'.$url.'/kh-live/sched_remote.php', 3);
	if ($remote_db!==false){
	$db=explode("\n",$remote_db);
	}else{
	echo '<div class="home_widget"><b style="color:red">ERROR</b>
	<br />Unable to connect to your congregation\'s server. Please let your administrator know.</div>';
	}
}
if (strlen(implode("", $db))>=10){
echo '<div class="home_widget"><b>Scheduled meetings :</b><br />';
$i=0;
    foreach($db as $line){
    if (strlen($line)>=10){
        $data=explode ("**",$line);
	$tmp=explode(':',$data[2]);
	if ($tmp[1]=='0') $data[2]=$data[2].'0';
	$tmp=explode(':',$data[3]);
	if ($tmp[1]=='0') $data[3]=$data[3].'0';
	
	if (($_SESSION['cong']==$data[0] OR $_SESSION['type']!='user')  AND $data[4]=='yes'){
	echo 'Meeting for '.$data[0].' : '.@$data[1].' ('.@$data[2].' - '.@$data[3].')<br />';
	}
	}
	$i++;
	}
echo '</div>';
}
}
//recoded meetings
if ($server_beta!="master"){
echo '<div class="home_widget"><b>Last recorded meetings :</b><br />';
$tmp_results=array();
 if ($dh = @opendir("./records")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 
	   if (strstr($file,$_SESSION['cong'])) {

	   $tmp_results[]=$file;
		
	   }
	   }
	   }
	   closedir($dh);
	}
rsort($tmp_results);
for ($i=0; $i<=2; $i++){
	if (isset($tmp_results[$i])){
 $file=$tmp_results[$i];
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
	   $tmptmp=explode('-', $file);
	   $tmpdate=explode('_',$tmptmp[1]);
	   $tmpyear=substr($tmpdate[0],0,4);
	   $tmpmonth=substr($tmpdate[0],4,2);
	   $tmpday=substr($tmpdate[0],6,2);
                     echo $tmpday.'.'.$tmpmonth.'.'.$tmpyear.' ('.$info.') <a href="./download.php?file='.$file.'">'.$lng['download'].'</a><br />';
	}
}
echo '<br /><a href="./record">Click here to view more...</a></div>';
}
//now we show the infos
$url='';
$db=array();
$infos_tmp='';
if ($server_beta!="master") {
if (file_exists("db/infos")){

$db=file("db/infos");
	}
}else{
	$db1=file("db/servers");
    foreach($db1 as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$url=$data[1];
	}
	}
if ($url!=""){
$remote_db=kh_fgetc_timeout('http://'.$url.'/kh-live/info_remote.php', 3);
	if ($remote_db!==false){
	$db=explode("\n",$remote_db);
	}else{
	echo '<div class="home_widget"><b style="color:red">ERROR</b>
	<br />Unable to connect to your congregation\'s info. Please let your administrator know.</div>';
	}
}
}
if (strlen(implode("", $db))>=10){
foreach($db as $line){
if (strlen($line)>=10){
        $data=explode ("**",$line);
	if ($data[3]=='yes' AND ($data[0]=='all' OR $data[0]==$_SESSION['cong'])){
	if ($data[2]!=''){
		if (strstr($data[2],'http://') OR strstr($data[2],'https://')){
		$infos_tmp='<a class="home_widget_link" target="_blank" href="'.$data[2].'">'.$data[1].'</a>'.$infos_tmp;
		}else{
		$infos_tmp='<a class="home_widget_link" href="'.$data[2].'">'.$data[1].'</a>'.$infos_tmp;
		}
	}else{
	$infos_tmp='<div class="home_widget">'.$data[1].'</div>'.$infos_tmp;
	}
	}
	}
	}
echo $infos_tmp;
}
if ($_SESSION['type']!="user"){
	echo '<div class="home_widget"><b>Disk space</b>
	<br />';
	$bytes = disk_free_space(".");
    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
    $base = 1024;
    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
    echo 'Disk Space remaining : '.sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
    $bytes1 = disk_total_space(".");
    $class = min((int)log($bytes1 , $base) , count($si_prefix) - 1);
    echo 'Total Disk Space : '.sprintf('%1.2f' , $bytes1 / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
    $percent=floor($bytes/$bytes1*100);
    if ($percent < 5){
    echo '<b style="color:red">WARNING Percentage available : '.$percent.'%</b>';
    }else{
    echo 'Percentage available : '.$percent.'%';
    }
	echo '</div>';
	}
?>
</div>
