<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<div id="page">
<h2><?PHP echo $lng['error'];?></h2>
<?PHP echo $lng['noconnection'];?>
<br />
<?PHP echo $lng['contactadmin'];?>
<br /><br />Debug:<br />
IP Address of stream server :<?PHP echo gethostbyname("khlive.mooo.com");?><br />
</div>