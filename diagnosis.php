<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$cong_name=$_SESSION['cong'];
if(isset($_POST['submit'])){
	if($_POST['submit']=="Start test"){
//start meeting
	$info="Channel: Local/meet_me_".$cong_name."_admin@test-menu
MaxRetries: 0
WaitTime: 30
Context: test-menu
Extension: test_meeting_".$cong_name."
Priority: 1
";
$file=fopen('/tmp/meeting_'.$cong_name.'_admin.call','w');
			if(fputs($file,$info)){
			fclose($file);
			rename('/tmp/meeting_'.$cong_name.'_admin.call', $asterisk_spool.'outgoing/meeting_'.$cong_name.'_admin.call');
	$file=fopen($temp_dir.'meeting_'.$_SESSION['cong'],'w');
			fputs($file,"live");
			fclose($file);
			$_SESSION['meeting_status']="live";
			$file=fopen($temp_dir.'test_meeting_'.$_SESSION['cong'],'w');
			fputs($file,"live");
			fclose($file);
			$_SESSION['test_meeting_status']="live";
			  echo 'Starting...<br /><br />';
			$info=time().'**info**test start**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			$info=time().'**info**test start**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
}elseif($_POST['submit']=="Stop test"){
//Local/meet_me_'.$cong_name.'_admin@test-menu
//doesnt work on debian
	$client='_'.$cong_name;
			exec($asterisk_bin.' -rx "core show channels concise"',$conf_db);
		foreach ($conf_db as $line){
		$data=explode("!",$line);
		if (strstr($data[0],$client)) {
		$kill=$data[0];
		exec($asterisk_bin.' -rx "channel request hangup '.$kill.'"');
		}
		}
	
			$file=fopen($temp_dir.'meeting_'.$_SESSION['cong'],'w');
			fputs($file,"down");
			fclose($file);
			$_SESSION['meeting_status']="down";
			$file=fopen($temp_dir.'test_meeting_'.$_SESSION['cong'],'w');
			fputs($file,"down");
			fclose($file);
			$_SESSION['test_meeting_status']="down";
		/*if we are streaming mp3 we mus still kill the stream proc*/
		exec('ps -eo pid,user,args',$stream_pid_list);
		$next="";
		foreach ($stream_pid_list as $pid_line){
			if ($next=="ok"){
			if (strstr($pid_line, "cat /dev/fd/3")) exec('kill '.$pid );
			$next="";
			}
			/*only works if there is only one mp3 stream per cong*/
			/*in alpine the output of PS is different than on debian*/
			/*the shell script doesn't always become defunct before ps is called*/
			if (strstr($pid_line, "{mp3stream-".$_SESSION['cong']."}") OR strstr($pid_line, "mp3stream-".$_SESSION['cong'].".sh") OR strstr($pid_line, "<defunct>")){
			$pids=explode("asterisk",$pid_line);
			$pid=$pids[0]+1;
			$next="ok";
			}
		}
		echo 'Stopping...<br /><br />';
		$info=time().'**info**test stop**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			$info=time().'**info**test stop**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}elseif($_POST['submit']=="Reboot"){
	$info=time().'**info**reboot**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
		exec('sudo reboot', $reboot);	
	}
}
?>
<div id="page">
<?PHP
//admin or root
if (is_array($reboot)){
echo '<br /><br />rebooting ...<br /><br />'.print_r($reboot);
}else{
echo '<h2>Test meeting :</h2>Start/stop a test meeting, if you want to test the system without needing the congregation\'s computer to be on.<br />Do not start a test while a real meeting is live as it may break things.<br /><br />';
echo '<form action="" method="post">';
if (strstr($_SESSION['meeting_status'],"down") AND strstr($_SESSION['test_meeting_status'],"down")){
	echo '<input name="submit" type="submit" value="Start test">';
	}elseif (strstr($_SESSION['test_meeting_status'],"live")){
	echo '<input name="submit" type="submit" value="Stop test">';
	}else{
	echo '<i>There is a meeting right now. You can\'t use the test meeting while there is a real meeting.</i>';
	}
	echo '</form>';
	//we dont do the test when we start the test meeting
if(!isset($_POST['submit']) AND $server_beta==true){
	$ping='';
	$dns='';
	$iax='';
	$sip='';
	//ping takes too long when it fails and is inaccurate
	/*exec('ping -c 1 4.2.2.2',$ping_result);
	$ping_result=implode(" , ",$ping_result);
	if (strstr($ping_result, "1 received")){
	$ping='<b style="color:green;">connected</b>';
	}else{
	$ping='<b style="color:red;">disconnected</b><br /><i style="font-size:12px;background-color:grey;">'.$ping_result.'</i>';
	}*/
	$dns_result=gethostbyname($test_url);
	if (!strstr($dns_result, $test_url)){
	$dns='<b style="color:green;">connected</b>';
	}else{
	$dns='<b style="color:red;">disconnected</b><br />';
	}
	exec('ping -c 1 '.$test_ip, $local_result);
	$local_result=implode(" , ",$local_result);
	//on centos  and debian the answer is : "1 received " on alpine linux : "1 packets received"
	if (strstr($local_result, "1 packets received") OR strstr($local_result, "1 received")){
	$local='<b style="color:green;">connected</b>';
	}else{
	$local='<b style="color:red;">disconnected</b><br /><i style="font-size:12px;background-color:grey;">'.$local_result.'</i>';
	}
	/*exec('asterisk -rx "iax2 show registry"',$iax_result);
	$iax_result=implode(" , ",$iax_result);
	if (strstr($iax_result, "Registered")){
	$iax='<b style="color:green;">connected</b>';
	}else{
	$iax='<b style="color:red;">disconnected</b><br /><i style="font-size:12px;background-color:grey;">'.$iax_result.'</i>';
	}*/
	/*exec($asterisk_bin.' -rx "sip show peers"',$sip_result);
	$sip_result=implode(" , ",$sip_result);
	if (strstr($sip_result, "Monitored: 1 online,")){
	$sip='<b style="color:green;">connected</b>';
	}else{
	$sip='<b style="color:red;">disconnected</b><br /><i style="font-size:12px;background-color:grey;">'.$sip_result.'</i>';
	}*/
echo '<div id="diags"><h2>Diagnosis</h2>
<b>Internet connectivity :</b><br />
Dns server : '.$dns.'<br />
<b>Local network connectivity :</b><br />
Local router : '.$local.'<br />';
/*<b>VOIP internet link connectivity:</b><br />
IAX trunk : '.$iax.'<br />*/
/*<b>SIP local link connectivity:</b><br />
Congregation computer : '.$sip.'<br />*/
echo '<h1>Reboot</h1>
<p>do not reboot if there is a meeting live! You might loose connection until the server restarts!</p>
<form action="" method="post">
<input name="submit" type="submit" value="Reboot" />
</form>
</div>';
}
}
echo "</div>";
?>
