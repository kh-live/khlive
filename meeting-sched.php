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
					if (($meeting_type=="direct" OR $meeting_type=='direct-stream')){
						$path=$temp_dir;
						if (is_dir($path)){
							if ($dh = @opendir($path)) {
								while (($file = readdir($dh)) !== false) {
									if (($file != '.') && ($file != '..')){
										if (!is_dir($path . $file)){
											if (strstr($file, "meeting_")){
												$content=implode("",file($path . $file));
												if (strstr($content, 'live')) {
													$already_meeting=$file;
												}
											}
										}
									}
								}
							closedir($dh);
							}
						}
					}else{
			if (isset($_SESSION['cong_phone_no'])){
				if ($_SESSION['cong_phone_no']!="" AND $meeting_type!="none"){
					$tmp_sip="";
					if ($sip_caller_ip!="") $tmp_sip=" ( ".$sip_caller_ip." ) ";
					//check if the call can be placed first warn if it can't
					if ($meeting_type=="sip"){
						exec($asterisk_bin.' -rx "sip show peers"',$sip_result);
						$tmp_unspec="(Unspecified)";
					}elseif ($meeting_type=="iax"){
						exec($asterisk_bin.' -rx "iax2 show peers"',$sip_result);
						$tmp_unspec="(null)";
					}
					$sip_result=implode(" , ",$sip_result);
					if (strstr($sip_result, "does /var/run/asterisk/asterisk.ctl exist?")){
						//echo 'Asterisk died. contact your administrator!';
						$already_meeting='asterisk error';
					}else{
						$sip_result2=str_replace(" ", "", $sip_result);
						if ($sip_caller_ip!=""){
							if (strstr($sip_result2, $_SESSION['cong_phone_no'].$sip_caller_ip)){
								/*echo '<b style="color:green;">The number seems to be reachable.</b><br /><br />';
								echo '<form action="" method="post">
								<input name="submit" id="input_login" type="submit" value="Start meeting">
								</form>';*/
							}else{
								//echo '<b style="color:red;">The number seems to be unreachable! The meeting won\'t start!<br />Please make sure that the Softphone (jitsy) is started OR restart the computer.</b><br /><br />';
								$already_meeting='number unreachable';
							}
						}else{
							//as we dont have an ip to compare to, we check that the phone no is unregistered
							// this breaks when asterisk died
							//$sip_result2, $_SESSION['cong_phone_no']."/".$_SESSION['cong_phone_no'].$tmp_unspec
							//that doesnt work on debian
							if (!strstr($sip_result2, $_SESSION['cong_phone_no'].$tmp_unspec)){
								/*echo '<b style="color:green;">The number seems to be reachable.</b><br /><br />';
								echo '<form action="" method="post">
								<input name="submit" id="input_login" type="submit" value="Start meeting">
								</form>';*/
							}else{
								//echo '<b style="color:red;">The number seems to be unreachable! The meeting won\'t start!<br />Please make sure that the Softphone (jitsy) is started OR restart the computer.</b><br /><br />';
								$already_meeting='number unreachable';
							}
						}
					}
				}
			}
					}
					if ($already_meeting==''){
						if (!strstr($_SESSION['meeting_status'],"live") AND $_SESSION['test_meeting_status']!="live" ){
							//we need to check that the time is accurate
					
							//we include the start file
							$meeting_processor='scheduler';
							include 'meeting-start.php';
							$info=time().'**info**schedule meeting launched successful**'.$cong."**\n";
							$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
							if(fputs($file,$info)){
								fclose($file);
							}
							//we must move that to dev/shm otherwise we'll loose the info when session is closed
							$_SESSION['meeting_start_time']=time();
						}else{
							$info=time().'**error**schedule meeting start failed : meeting stat : '.$_SESSION['meeting_status'].' - test meeting stat : '.$_SESSION['test_meeting_status'].'**'.$cong."**\n";
							$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
							f(fputs($file,$info)){
								fclose($file);
							}
						}
					}else{
						$info=time().'**error**schedule meeting launch failed - '.$already_meeting.'**'.$cong."**\n";
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
					
					if (strstr($_SESSION['meeting_status'],"live") AND $_SESSION['test_meeting_status']!="live"){
						//we need to check that the time is accurate
					
						//we include the ajax file
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
				}
			}
		}
	
	}
}
?>