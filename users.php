<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if (isset($_GET['clear'])){
$client_id=$_GET['clear'];
$listener='';
$db=file("db/live_users");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$client_id){
		$listener=$data[1];
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/live_users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo 'listener cleared';
			}
			$info=time().'**info**listener cleared**'.$listener.'('.$client_id.') by '.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
}
?>
<div id="page">
<h2><?PHP echo $lng['users'];?></h2>
<a href="./user_add"><?PHP echo $lng['add_new_user'];?></a><br /><br />
<table>
<?PHP
echo '<tr><td><b>'.$lng['user'].'</td><td><b>'.$lng['name'].'</b></td><td><b>Info</b></td><td><b>Last login on</b></td><td><b>'.$lng['congregation'].'</b></td><td><b>'.$lng['rights'].'</b></td><td><b>type</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
	//if there is no date data6 contains a \n
	if (file_exists('./logins/'.md5($data[0]))){
	$last_login=date('d/m/Y',implode("", file('./logins/'.md5($data[0]))));
	}else{
	$last_login="-";
	}
	if ($server_beta=="master"){
		if ($_SESSION['type']!="root" AND $data[4]=="root" AND $_SESSION['cong']==$data[3] ){
		//we dont let normal admins edit superusers
		echo '<tr><td>'.$data[0].'</td><td>'.$data[2].'</td><td>'.@$data[8].'</td><td>'.$last_login.'</td><td>'.str_replace('_',' ',$data[3]).'</td><td>Administrator</td><td>'.$data[6].'</td><td></td></tr>';
		}elseif ($_SESSION['cong']==$data[3] OR $_SESSION['user']=="lionel"){
		echo '<tr><td>'.$data[0].'</td>';
	$tmp="user_".$data[4];
	echo '<td>'.$data[2].'</td><td>'.@$data[8].'</td><td>'.$last_login.'</td><td>'.str_replace('_',' ',$data[3]).'</td><td>'.$lng[$tmp].'</td><td>'.$data[6].'</td><td><a href="./user_edit?user='.urlencode($data[0]).'">'.$lng['edit'].'</a> - <a href="./user_delete?user='.urlencode($data[0]).'&pin='.$data[5].'">'.$lng['delete'].'</a></td></tr>
	';
		}
	}else{
		if ($_SESSION['type']!="root" AND $data[4]=="root" AND $_SESSION['cong']==$data[3] ){
		//we dont let normal admins edit superusers
		echo '<tr><td>'.$data[0].'</td><td>'.$data[2].'</td><td>'.@$data[8].'</td><td>'.$last_login.'</td><td>'.str_replace('_',' ',$data[3]).'</td><td>Administrator</td><td>'.$data[6].'</td><td></td></tr>';
		}elseif ($_SESSION['type']=="root" OR $_SESSION['cong']==$data[3]){
		//root sees everything and admins can see their congreg.
	echo '<tr><td>'.$data[0].'</td>';
	$tmp="user_".$data[4];
	echo '<td>'.$data[2].'</td><td>'.@$data[8].'</td><td>'.$last_login.'</td><td>'.str_replace('_',' ',$data[3]).'</td><td>'.$lng[$tmp].'</td><td>'.$data[6].'</td><td><a href="./user_edit?user='.urlencode($data[0]).'">'.$lng['edit'].'</a> - <a href="./user_delete?user='.urlencode($data[0]).'&pin='.$data[5].'">'.$lng['delete'].'</a></td></tr>
	';
	
		}
	}
	}
	?>
	</table>
	<?PHP
	if ($_SESSION['type']=="root"){
	?>
	<h2><?PHP echo $lng['live_users'];?></h2>
<?PHP echo $lng['live_users_txt'];?><br /><br />
<table>
<?PHP
echo '<tr><td><b>'.$lng['user'].'</b></td><td><b>'.$lng['congregation'].'</b></td><td><b>'.$lng['stream'].'</b></td><td><b>'.$lng['date_started'].'</b></td><td><b>'.$lng['status'].'</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
$db=file("db/live_users");
	if (count($db)==0){
	echo '<tr><td>'.$lng['no_live_users'].'</td><td></td><td></td><td></td><td></td><td></td>';
	}else{
    foreach($db as $line){
        $data=explode ("**",$line);
	echo '<tr><td>'.$data[1].'</td><td>'.$data[2].'</td><td>'.$data[3].'</td><td>'.date('H:i:s-d/m/Y',$data[4]).'</td><td>'.$data[5].'</td><td><a href="./users?clear='.$data[0].'">Clear</a></td>';
	}
	}
	?>
</table>
<?PHP
}
?>
</div>
