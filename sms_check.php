<?PHP
if (isset($_GET['usr']) AND isset($_GET['cong']) AND isset($_GET['khuid'])){
$usr=$_GET['usr'];
$cong=$_GET['cong'];
$khuid=$_GET['khuid'];

$db=file("db/live_users");
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[7]==$khuid){
			$tmp=explode("--",$data[5]);
				if ($tmp[0]=="normal"){
				//asnwer has been read
				echo "closed";
				}elseif ($tmp[0]=="request"){
				//answer is received
				echo "received";
				}elseif ($tmp[0]=="answering"){
				//answer is waiting
				echo "waiting";
				}
			}
		}
}
?>