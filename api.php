<?PHP
include ("config.php");
if (isset($_GET['q'])){
	$query=explode("**", urldecode($_GET['q']));
	if ($query[0]==$api_key){
		if ($query[1]=="status"){
		echo "ok";
		exit;
		}
	}
}
?>