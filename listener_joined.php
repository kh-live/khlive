<?PHP
if (isset($cong2)) unset($cong2);
if (isset($cong)) unset($cong);
 if (isset($_POST['action'])){
	if ($_POST['action']=="listener_add"){
	//this is called by icecast when someone asks for the stream
	$mount=$_POST['mount'];
	$server=$_POST['server'];
	$port=$_POST['port'];
	$client_id=$_POST['client']; //client id within icecast
	$ip_address=$_POST['ip'];
	$agent=$_POST['agent'];
	//this is a bit of a dangerous way of getting the params. What if the order changes...
	$query=explode("?",$mount);
	$params=explode("&",$query[1]);
	$user_string=explode("=",$params[0]);
	$user=urldecode($user_string[1]);
	$cong_string=explode("=",$params[1]);
	$congregation=urldecode($cong_string[1]);
	$khuid_string=explode("=",$params[2]);
	$khuid=urldecode($khuid_string[1]);
	$mount=$query[0]; //overwrites mount
	
	$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$user) {
	$cong=$data[3];
	}
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
	$newname="";
	$info=$client_id.'**'.$user.'**'.$congregation.'**'.$mount.'**'.time()."**normal****".$khuid."**".$newname."**\n";
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
	//this is called by asterisk when someone connects over the phone
	$cong=urldecode($_GET['cong']);
	$client=urldecode($_GET['client']);
	$type=urldecode($_GET['type']);
	$conf_id=urldecode($_GET['confid']);
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
	$info=$conf_id.'**'.$client.'**'.$cong.'**'.$type.'**'.time()."**normal********\n";
	$file=fopen('./db/live_users','a');
			if(fputs($file,$info)){
			fclose($file);
			}
  }elseif  ($_GET['action']=="update_at"){
	// this is called by ajax script on user side
	if (isset($_GET['number']) AND isset($_GET['user']) AND isset($_GET['cong']) AND isset($_GET['khuid'])){
	$number=urldecode($_GET['number']);
	$user=urldecode($_GET['user']);
	$cong=urldecode($_GET['cong']);
	$khuid=urldecode($_GET['khuid']);
	
	$a = session_id();
if ($a == ''){
session_start();
}
	$_SESSION['number_at']=$number;
		
		$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong) $cong2=$cong;
	}
	
	$file=file('./db/live_users');
	$new_file="";
	foreach($file as $line){
	$live_user=explode("**",$line);
		if ($live_user[1]==$user AND $live_user[7]==$khuid){
		$new_file.=$live_user[0]."**".$live_user[1]."**".$live_user[2]."**".$live_user[3]."**".$live_user[4]."**".$live_user[5]."**".$number."**".$live_user[7]."**".$live_user[8]."**\n";
		}else{
		$new_file.=$line;
		}
	}
	$file2=fopen('./db/live_users','w');
			if(fputs($file2,$new_file)){
			fclose($file2);
			echo "ok";
			}
	$info=time().'**info**attendance report**'.$user."**".$number."**\n";
	$file=fopen('./db/logs-'.strtolower($cong2).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
  }elseif  ($_GET['action']=="update_name"){
	// this is called by ajax script on user side
	if (isset($_GET['newname']) AND isset($_GET['user']) AND isset($_GET['cong']) AND isset($_GET['khuid'])){
	$newname=urldecode($_GET['newname']);
	$user=urldecode($_GET['user']);
	$cong=urldecode($_GET['cong']);
	$khuid=urldecode($_GET['khuid']);
	
	$a = session_id();
if ($a == ''){
session_start();
}
	$_SESSION['new_name']=$newname;
		
		$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong) $cong2=$cong;
	}
	
	$file=file('./db/live_users');
	$new_file="";
	foreach($file as $line){
	$live_user=explode("**",$line);
		if ($live_user[1]==$user AND $live_user[7]==$khuid){
		$new_file.=$live_user[0]."**".$live_user[1]."**".$live_user[2]."**".$live_user[3]."**".$live_user[4]."**".$live_user[5]."**".$live_user[6]."**".$live_user[7]."**".$newname."**\n";
		}else{
		$new_file.=$line;
		}
	}
	$file2=fopen('./db/live_users','w');
			if(fputs($file2,$new_file)){
			fclose($file2);
			echo "ok";
			}
	$info=time().'**info**attendance report**'.$user."**".$number."**\n";
	$file=fopen('./db/logs-'.strtolower($cong2).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
  }
 }
?>
