<?PHP
//we need db/config.php for this to work
//we can't stop a meeting that's started by a relay
if ($meeting_type!="none"){
$_SESSION['meeting_stop_time']=time();
	if (is_file($temp_dir.'song_1_'.$_SESSION['cong'])){
unlink($temp_dir.'song_1_'.$_SESSION['cong']);
}
if (is_file($temp_dir.'song_2_'.$_SESSION['cong'])){
unlink($temp_dir.'song_2_'.$_SESSION['cong']);
}
if (is_file($temp_dir.'song_3_'.$_SESSION['cong'])){
unlink($temp_dir.'song_3_'.$_SESSION['cong']);
}
	/*this only works if the call was initiated on sip*/
	if ($meeting_type=="sip"){
	$client='SIP/'.$_SESSION['cong_phone_no'];
	}elseif ($meeting_type=="iax"){
	$client='IAX2/'.$_SESSION['cong_phone_no'];
	}elseif ($meeting_type=="direct" AND $server_audio=="alsa"){
	$client='ALSA/'.$alsa_in;
	}elseif ($meeting_type=="direct" AND $server_audio=="dsp"){
	$client='Console/'.$server_audio;
	}
	if ($meeting_type!='direct-stream'){
			exec($asterisk_bin.' -rx "core show channels concise"',$conf_db);
		foreach ($conf_db as $line){
		$data=explode("!",$line);
		if (strstr($data[0],$client)) $kill=$data[0];
		}
		if ($server_beta=='true') $kill="testing";
		if (isset($kill)){
	exec($asterisk_bin.' -rx "channel request hangup '.$kill.'"');
	/*if we are streaming mp3 we must still kill the stream proc*/
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
		//if the start time is not set we default to 2 hours
		if (!file_exists($temp_dir.'start_'.$_SESSION['cong'])){
		$_SESSION['meeting_start_time']=time()-7200;
		}else{
		$_SESSION['meeting_start_time']=implode("",file($temp_dir.'start_'.$_SESSION['cong']));
		}
		$meeting_length=$_SESSION['meeting_stop_time'] - $_SESSION['meeting_start_time'];
		//26 is the no of seconds of recording encode by the server in one second on PI B+
		if (!isset($encoder_speed)) $encoder_speed=26;
		$time_to_encode = $meeting_length/$encoder_speed;
	if ($meeting_processor!='scheduler'){
		echo '<b style="color:red;">Please wait for the page to reload before doing anything else!</b><br />Stopping streams : Done <br />
		Encoding Recording to MP3 : <b id="progress_percent">0%</b><br /><br /><progress id="progress_recording" value=0" max="100"></progress><br /><br />
		<script>
		var timeLeft='.$time_to_encode.';
		function animate_progress(time){
		var percentDone=Math.round(100 * time / timeLeft);
		if (percentDone>100){
		percentDone=100;
		}
		document.getElementById("progress_recording").value= percentDone;
		document.getElementById("progress_percent").innerHTML= percentDone + "%";
		var time2=time + 5;
		if (time<timeLeft){
		setTimeout(function() { animate_progress(time2); }, 5000);
		}else{
		 window.location= "./meeting-ajax.php";
		}
		}
		animate_progress(0);
		</script>';
	}
		$_SESSION['meeting_just_stopped']=1;
			$info=time().'**info**meeting stop**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			}
	}else{
	//this is direct-stream
	//we must only kill the script then log
	exec('ps -eo pid,user,args',$stream_pid_list);
	
		foreach ($stream_pid_list as $pid_line){
			
			if (strstr($pid_line, "arecord")){
			$pids=explode("asterisk",$pid_line);
			$pid=$pids[0]; /*$pid=$pids[0]+1; why +1? it should be the pid line not next one...*/
			exec('kill '.$pid );
			}
		}
	
	$_SESSION['meeting_just_stopped']=1;
			$info=time().'**info**meeting stop**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			//it takes about 5 sec to stop the meeting this way
		if ($meeting_processor!='scheduler'){
			echo ' Stopping...<br /><br />
			<script>
			setTimeout(function(){ window.location= "./meeting-ajax.php"},10000);
			</script>';
		}
	}
}
?>