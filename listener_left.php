<?PHP
if (isset($cong2)) unset($cong2);
if (isset($cong)) unset($cong);
 if (isset($_POST['action'])){
	if ($_POST['action']=="listener_remove"){
	$mount=$_POST['mount'];
	$server=$_POST['server'];
	$port=$_POST['port'];
	$client_id=$_POST['client']; //client id within icecast
	$duration=$_POST['duration'];
	$query=explode("?",$mount);
	$params=explode("&",$query[1]);
	$user_string=explode("=",$params[0]);
	$user=$user_string[1];
	$cong_string=explode("=",$params[1]);
	$congregation=$cong_string[1];
	$mount=$query[0]; //overwrites mount
	
	$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$user) $cong=$data[3];
	}
	
	if (isset($cong)){
	$info=time().'**info**listener left**'.$mount.'@'.$server.':'.$port.'**'.$user.'@'.$congregation.'**'.$client_id.'--'.$duration."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	
	$info=time().'**info**listener left**'.$user.'**'.$duration."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}else{
	$info=time().'**warn**listener left**'.$mount.'@'.$server.':'.$port.'**'.$user.'@'.$congregation.'**'.$client_id.'--'.$duration."--no cong linked to user**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
	$db=file("db/live_users");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$client_id){
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/live_users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}
	
	}
	}
 if (isset($_GET['action'])){

if ($_GET['action']=="phone_remove"){
	$cong=$_GET['cong'];
	$client=$_GET['client'];
	$type=$_GET['type'];
	
	$db=file("db/live_users");
			$duration="";
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$client){
		$duration=time()-$data[4];
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/live_users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}
	
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong) $cong2=$cong;
	}
	
	if (isset($cong2)){
	$info=time().'**info**listener left**'.$client.'**'.$cong.'**'.$type."--".$duration."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	
	$info=time().'**info**listener left**'.$client.'**'.$type."**".$duration."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}else{
	$info=time().'**warn**listener left**'.$client.'**'.$cong.'**'.$type."--".$duration."--no cong linked to user**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
	
	}
 }
?>
