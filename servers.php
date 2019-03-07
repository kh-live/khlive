<?PHP
set_time_limit(300);
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include ('functions.php');
?>
<div id="page">
<h2>Servers</h2>
<a href="./server_add">add new server</a><br /><br />
<table>
<?PHP
echo '<tr><td><b>server name</td><td><b>url</b></td><td><b>api</b></td><td><b>status</b></td><td><b>congregations</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
$db=file("db/servers");
    foreach($db as $line){
    $data=explode ("**",$line);
    if (strstr($data[0], 'sinux.ch' )){
    echo '<tr><td>'.$data[0].'</td><td>'.$data[1].'</td><td>'.$data[2].'</td><td>TEST</td><td>'.@$data[3].'</td><td><a href="./server_edit?server='.urlencode($data[0]).'">'.$lng['edit'].'</a> - <a href="./server_delete?server='.urlencode($data[0]).'">'.$lng['delete'].'</a></td></tr>
	';
    }else{
	if (isset($_SESSION['server_'.str_replace(".","",$data[1])])){
	$status=$_SESSION['server_'.str_replace(".","",$data[1])];
	}else{
	$key=$data[2];
	$string=time()."**status";
	//$encrypted=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	$encrypted=kh_encrypt($string,$key);
	$response=@file_get_contents('http://'.$data[1].'/kh-live/api.php?q='.urlencode($encrypted));
	if ($response!=""){
	//$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($response), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	$decrypted = kh_decrypt($response, $key);
	$dec=explode("@@@", $decrypted);
	if (@$dec[1]=="ok"){
	$status="<b style=\"color:green;\">LIVE - ".$dec[0]."</b>";
	}elseif(@$dec[0]=="timeout"){
	$status='<b style="color:orange;">TIME OUT OF SYNC - '.($dec[1]-time()).'</b>';
	}else{
	$status='<b style="color:red;">ERROR - '.$decrypted.' - '.$response.'</b>';
	}
	}else{
	$status="<b style=\"color:grey;\">OFFLINE</b>";
	}
	$_SESSION['server_'.str_replace(".","",$data[1])]=$status.' (cached)';
	}
	echo '<tr><td>'.$data[0].'</td><td>'.$data[1].'</td><td>'.$data[2].'</td><td>'.$status.'</td><td>'.@$data[3].'</td><td><a href="./server_edit?server='.urlencode($data[0]).'">'.$lng['edit'].'</a> - <a href="./server_delete?server='.urlencode($data[0]).'">'.$lng['delete'].'</a></td></tr>
	';
	}
	}
	?>
	</table>

</div>
