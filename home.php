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
}
?>
<div id="page">
<h2><?PHP echo $lng['home'];?></h2>
<?PHP
echo $lng['welcome'].' <b>'.$_SESSION['full_name'].'</b> !<br />';
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
echo '</select><br /><br />';

}
if ($_SESSION['type']!="manager") echo "Your quick login PIN is : <b>".$_SESSION['pin']."#</b><br /><br />";

if ($_SESSION['type']!="user"){
echo 'Use the link "Meeting" above to manage the congregation\'s meeting.<br /><br />';
echo 'Use the link "Recordings" above to download the congregation\'s meetings in mp3.<br /><br />';
echo 'Use the link "Report" above to see your congregation\'s meeting attendance.<br /><br />';
}else{
echo $lng['welcome_instructions'].'<br /><br />';
echo $lng['listen_records'].'.<br />';
}
?>
</div>