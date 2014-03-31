<?PHP
if (isset($cong2)) unset($cong2);
if (isset($cong)) unset($cong);
 if (isset($_POST['action'])){
	if ($_POST['action']=="listener_add"){
	$mount=$_POST['mount'];
	$server=$_POST['server'];
	$port=$_POST['port'];
	$client_id=$_POST['client']; //client id within icecast
	$ip_address=$_POST['ip'];
	$agent=$_POST['agent'];
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
	$info=time().'**info**new listener**'.$mount.'@'.$server.':'.$port.'**'.$user.'@'.$congregation.'**'.$client_id.'@'.$ip_address.'**'.$agent."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**new listener**'.$user."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	
	}else{
	$info=time().'**warn**new listener**'.$mount.'@'.$server.':'.$port.'**'.$user.'@'.$congregation.'**'.$client_id.'@'.$ip_address.'**'.$agent."--no cong linked to the user...**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
	$info=$client_id.'**'.$user.'**'.$congregation.'**'.$mount.'**'.time()."**normal**\n";
	$file=fopen('./db/live_users','a');
			if(fputs($file,$info)){
			fclose($file);
			}
	//response to icecast
	header('icecast-auth-user: 1');
	
	}
	}
	
 if (isset($_GET['action'])){

if ($_GET['action']=="phone_add"){
	$cong=$_GET['cong'];
	$client=$_GET['client'];
	$type=$_GET['type'];
	$conf_id=$_GET['confid'];
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong) $cong2=$cong;
	}
	
	if (isset($cong2)){
	$info=time().'**info**new listener**'.$client.'**'.$cong."**".$type."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			
	$info=time().'**info**new listener**'.$client."**".$type."**\n";
	$file=fopen('./db/logs-'.strtolower($cong2).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	
	}else{
	$info=time().'**warn**new listener**'.$client.'**'.$cong."**".$type."--no cong linked to the user...**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
	$info=$client.'**'.$conf_id.'**'.$cong.'**'.$type.'**'.time()."**normal**\n";
	$file=fopen('./db/live_users','a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
 }
?>
