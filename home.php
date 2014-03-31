<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2><?PHP echo $lng['home'];?></h2>
<?PHP
echo $lng['welcome'].' <b>'.$_SESSION['full_name'].'</b> !<br />';
echo $lng['cong_part'].' : <b>'.$_SESSION['cong'].'</b>.<br /><br />';
if ($_SESSION['type']=="manager" OR $_SESSION['type']=="admin"){
echo 'Use the link "Meeting" above to manage the congregation\'s meeting.<br /><br />';
echo 'Use the link "Recordings" above to download the congregation\'s meetings in mp3.<br /><br />';
echo 'Use the link "Report" above to see your congregation\'s meeting attendance.<br /><br />';
}elseif ($_SESSION['type']=="user"){
echo $lng['welcome_instructions'].'<br /><br />';
echo $lng['listen_records'].'.<br />';
}
echo "</div>";
?>
