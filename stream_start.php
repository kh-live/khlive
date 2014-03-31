<?PHP
if (isset($cong)) unset($cong);
 if (isset($_POST['action'])){
	if ($_POST['action']=="mount_add"){
	$mount=$_POST['mount'];
	$server=$_POST['server'];
	$port=$_POST['port'];
	
	$db=file("db/streams");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$mount) $cong=$data[1];
	}
	
	if (isset($cong)){
	$info=time().'**info**new mount**'.$mount.'@'.$server.':'.$port.'**'.$cong."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	$info=time().'**info**new mount**'.$mount."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$cong) $type=$data[5];
	}
		if ($type=="none"){
		$file=fopen('/dev/shm/meeting_'.$cong,'w');
			fputs($file,"live");
		fclose($file);
		}
	}else{
	$info=time().'**warn**new mount**'.$mount.'@'.$server.':'.$port."**no cong linked to stream**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
	
	}
	$info=$mount.'**'.time()."**\n";
	$file=fopen('./db/live_streams','a');
			if(fputs($file,$info)){
			fclose($file);
	}
	}
 }
?>