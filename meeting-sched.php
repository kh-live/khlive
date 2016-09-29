<?PHP
if(session_id()==""){session_start();}
date_default_timezone_set ('Africa/Johannesburg');
include 'db/config.php';
if (isset($scheduler)){
	if ($scheduler=='yes'){
	chdir(dirname(__FILE__));
	$db=file('./db/sched');
		if ($db!=''){
			foreach($db as $line){
				$data=explode('**', $line);
				$cong=$data[0];
				$day=$data[1];
				$start_time=explode(':',$data[2]);
				$stop_time=explode(':',$data[3]);
				$enabled=$data[4];
				if (($enabled=='yes') AND (date('D',time())==$day) AND (date('G',time())==$start_time[0]) AND ( (1*date('i',time())==$start_time[1]) OR ((1*date('i',time())) +1 == $start_time[1]) OR ((1*date('i',time())) -1 == $start_time[1]) ) ){
					//we need to set all the required variables
					$_SESSION['cong']=$cong;
					$_SESSION['user']='automatic_scheduler';
					$db1=file("db/cong");
					foreach($db1 as $line){
						$data=explode ("**",$line);
						if ($data[0]==$_SESSION['cong']) {
							$meeting_type=$data[5];
							$sip_caller_ip=@$data[13];
							$record=$data[10];
							$stream_quality=$data[12];
							$bitrate=15+(3*$stream_quality);
							$stream_type=$data[7];
							$_SESSION['cong_phone_no']=$data[4];
						}
					}
					//we need to make sure that there is no other cong having a meeting already
					if (file_exists($temp_dir.'meeting_'.$_SESSION['cong'])){
						$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
					}else{
						$_SESSION['meeting_status']='down';
					}
					
					if (file_exists($temp_dir.'test_meeting_'.$_SESSION['cong'])){
						$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
					}else{
						$_SESSION['test_meeting_status']='down';
					}
					$already_meeting='';
					$meeting_processor='scheduler';
					$skip=0;
					include 'meeting-precheck.php';
					
					if ($already_meeting==''){
						if (!strstr($_SESSION['meeting_status'],"live") AND $_SESSION['test_meeting_status']!="live" ){
							//we include the start file
							$meeting_processor='scheduler';
							include 'meeting-start.php';
							$info=time().'**info**schedule meeting launched successful**'.$cong."**\n";
							$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
							if(fputs($file,$info)){
								fclose($file);
							}
							//we set when the meeting was started
							$now=time();
							$file=fopen($temp_dir.'start_'.$_SESSION['cong'],'w');
							fputs($file,$now);
							fclose($file);
							//we set when it's supposed to stop
							$duration = (($stop_time[0] - $start_time[0]) * 3600) + (($stop_time[1] - $start_time[1]) * 60);
							$endtime= $now + $duration;
							$file=fopen($temp_dir.'stop_'.$_SESSION['cong'],'w');
							fputs($file,$endtime);
							fclose($file);
						}else{
							$info=time().'**info**schedule meeting start skipped : meeting stat : '.$_SESSION['meeting_status'].' - test meeting stat : '.$_SESSION['test_meeting_status'].'**'.$cong."**\n";
							$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
							if(fputs($file,$info)){
								fclose($file);
							}
						}
					}else{
						$info=time().'**error**schedule meeting start failed - Reason : '.$already_meeting.'**'.$cong."**\n";
							$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
							if(fputs($file,$info)){
								fclose($file);
							}
					}
				}elseif (($enabled=='yes') AND (date('D',time())==$day) AND (date('G',time())==$stop_time[0]) AND ( (1*date('i',time())==$stop_time[1]) OR ((1*date('i',time())) +1 == $stop_time[1]) OR ((1*date('i',time())) -1 == $stop_time[1]) ) ){
					//we need to set all the required variables
					$_SESSION['cong']=$cong;
					$_SESSION['user']='automatic_scheduler';
					$db=file("db/cong");
					foreach($db as $line){
						$data=explode ("**",$line);
						if ($data[0]==$_SESSION['cong']) {
							$meeting_type=$data[5];
							$sip_caller_ip=@$data[13];
							$record=$data[10];
							$stream_quality=$data[12];
							$bitrate=15+(3*$stream_quality);
							$stream_type=$data[7];
							$_SESSION['cong_phone_no']=$data[4];
						}
					}
					//we need to make sure that there is still a meeting to stop
					if (file_exists($temp_dir.'meeting_'.$_SESSION['cong'])){
						$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
					}else{
						$_SESSION['meeting_status']='down';
					}
					
					if (file_exists($temp_dir.'test_meeting_'.$_SESSION['cong'])){
						$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
					}else{
						$_SESSION['test_meeting_status']='down';
					}
					$bypass_time=0;
					if (file_exists($temp_dir.'bypass_'.$_SESSION['cong'])){
						$bypass_time=implode("",file($temp_dir.'bypass_'.$_SESSION['cong']));
					}
					$time_since_bypass= time() - $bypass_time ;
					if ($time_left <= (5*60) AND $time_since_bypass >= (10*60*60)){					
					if (strstr($_SESSION['meeting_status'],"live") AND $_SESSION['test_meeting_status']!="live"){
						//we include the stop file
						$meeting_processor='scheduler';
						include 'meeting-stop.php';
						$info=time().'**info**schedule meeting stopped successful**'.$cong."**\n";
						$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
						if(fputs($file,$info)){
							fclose($file);
						}
					}else{
						$info=time().'**error**schedule meeting stop failed : meeting stat : '.$_SESSION['meeting_status'].' - test meeting stat : '.$_SESSION['test_meeting_status'].'**'.$cong."**\n";
						$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
						if(fputs($file,$info)){
							fclose($file);
						}
					}
					}else{
						$info=time().'**info**schedule meeting stop bypassed**'.$cong."**\n";
						$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
						if(fputs($file,$info)){
							fclose($file);
						}
					}
				}
			}
		}
	
	}
}
?>