<?PHP
include 'db/config.php';

if ($server_beta=='master'){
if (isset($_GET['cong'])){
	$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3], $_GET['cong'])){
	$url_cong=$data[0];
	}
  }
}
if (isset($url_cong)){
	//we must include the remote timing related to that cong
$url = 'http://'.$url_cong.'/kh-live/time';

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$result = curl_exec($ch);

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
}
curl_close($ch);
echo str_replace("window.location='./time';","window.location='./remote.php?cong=".$_GET['cong']."';",$result);

	}
	}
die();
?>