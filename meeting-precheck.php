<?PHP
//we check that the internet connection is working and the clock synchronised. We warn if it's not.
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
$test_time=@file_get_contents('http://kh-live.co.za/time.php',false,$context);
if ($test_time!==FALSE){
	if (is_numeric($test_time)){
		$now=time();
		$min=$now - 60 ;
		$max=$now + 60;
		if (($min <= $test_time) AND ($test_time <= $max)){
			if ($meeting_processor!='scheduler' AND @$scheduler=='yes' AND $meeting_type!='none'){
			//we display when the next schedulded meeting is going to take palce
			echo '<br /><i style="background-color:rgba(0,0,0,0.3);display:block;">Scheduled meetings for today (automatic start and stop) :<br />';
			$smeetings='';
			$db0=file('./db/sched');
			if ($db0!=''){
			foreach($db0 as $line){
				$data=explode('**', $line);
				$cong=$data[0];
				$day=$data[1];
				$start_time=explode(':',$data[2]);
				$stop_time=explode(':',$data[3]);
				$enabled=$data[4];
				if (($enabled=='yes') AND (date('D',time())==$day)){
					$smeetings.= '- <b>'.$cong.'</b> meeting from '.sprintf('%02d:%02d', $start_time[0],$start_time[1]).' to '.sprintf('%02d:%02d', $stop_time[0],$stop_time[1]).' </br>';
				}
				}
			}
			if ($smeetings!=''){
			echo $smeetings;
			}else{
			echo 'No meeting scheduled for today';
			}
			echo '</i><br />';
			//countdown
				if ($smeetings!=''){
					$temp_meetings=explode ('</br>', $smeetings);
					foreach($temp_meetings as $temp_meeting){
					$temp_cong=explode('</b>',$temp_meeting);
					
					$temp_cong2=explode('<b>',$temp_cong[0]);
						if (@$temp_cong2[1]==$_SESSION['cong']){
							$temp_start=explode(' to ',$temp_meeting);
							$temp_start2=explode('from ',$temp_start[0]);
							$temp_start3=explode(':',$temp_start2[1]);
							$temp_hour=$temp_start3[0];
							$temp_min=$temp_start3[1];
							
							$today=date('d.m.Y', time());
							$start_timestamp=strtotime($today." ".$temp_start2[1]);
							if ($start_timestamp > time()){
								$time_min_left=((($start_timestamp-time())/60) % 60);
								$time_hour_left=(($start_timestamp-time())/3600) % 24;
								if ($time_hour_left == 0 AND $time_min_left == 0) {
									echo 'Starting...<br />Please wait 60 seconds for the page to refresh.<br /><br />';
								}else{
									echo 'Automatically Starting in : ';
								
									if ($time_hour_left > 0) echo $time_hour_left.' h and ';
									if ($time_min_left >= 0) echo $time_min_left.' min<br /><br />';
								}
							}
						}
					}
				}
			}
		}else{
			if ($meeting_processor!='scheduler'){
				echo '<br /><b style="background-color:orange;color:black;display:block;">Note : The clock is not synchronised! The automatic scheduler wont trigger.</b><br />';
			}else{
				$already_meeting='clocks_not_synched';
			}
		}
	}elseif ($meeting_processor=='scheduler'){
		$already_meeting='could_not_get_remote_time';
	}else{
		echo '<br /><b style="background-color:orange;color:black;display:block;">Note : Cant get remote time! The automatic scheduler wont trigger.</b><br />';
	}
}else{
	if ($meeting_processor!='scheduler'){
	echo '<br /><b style="background-color:red;color:white;display:block;">Warning! It seems you are not connected to the internet.<br />
	If you start the meeting now, it will be recorded but the streaming wont be available.<br />
	Check the following : <br />
	1. Your databundle is not finished<br />
	2. Reboot the router<br />
	3. Ask your administrator to reboot the server</b><br />';
	}else{
	$already_meeting='no_internet_connection';
	}
}
$already_meeting1='';
if (($meeting_type=="direct" OR $meeting_type=='direct-stream')){
			if ($skip==0){
				$already_meeting='';
				$path=$temp_dir;
				if (is_dir($path)){
					if ($dh = @opendir($path)) {
						while (($file = readdir($dh)) !== false) {
							if (($file != '.') && ($file != '..')){
								if (!is_dir($path . $file)){
									if (strstr($file, "meeting_")){
										$content=implode("",file($path . $file));
										if (strstr($content, 'live')) {
											//we prevent starting the meeting only if that congregation also uses direct or direct-stream
											$db1=file('./db/cong');
												if ($db1!=''){
												foreach($db1 as $line){
												$data=explode('**', $line);
												$cong=$data[0];
												$tmp_file=str_replace('test_meeting_','',$file);
												$tmp_file1=str_replace('meeting_','',$tmp_file);
													if (str_replace(' ', '_',$cong)==$tmp_file1){
														if (($data[5]=="direct" OR $data[5]=='direct-stream')){
														$already_meeting1=$file;
														}
													}
												}
												}else{
												$already_meeting1='cant read cong db';;
												}
											
										}
									}
								}
							}
						}
						closedir($dh);
					}
				}
				if ($meeting_processor!='scheduler'){
				echo 'Click on the button bellow to start the meeting manually.<br /><b style="color:green;">We\'ll try to connect to the server\'s sound card...</b><br /><br />';
				if ($already_meeting1==''){
					echo '<form action="" method="post">
					<input name="otp_time" type="hidden" value="'.time().'" />
					<input name="submit" id="input_login" type="submit" value="Start meeting" />
					</form>';
				}elseif(strstr($already_meeting1,$_SESSION['cong'])){
					echo ' The meeting is busy starting...<br/><a href="./meeting-ajax.php">Click here to refresh manually</a>';
				}else{
					echo '<b style="color:red;">there is already a meeting on this server started by : '.$already_meeting1.'</b><br />Terminate that one first before you can start yours.<br />';
				}
				}
				if ($already_meeting1!=''){
				$already_meeting=$already_meeting1;
				}
			}
		}elseif ($meeting_type=='none'){
			if ($meeting_processor!='scheduler'){
			echo 'Press the "connect" button on Edcast/BoradcastMyself to start the meeting.<br />The meeting wont be recorded on the server side.<br /> You have to record it yourself (with Audactiy or Edcast).';
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
						if ($meeting_processor!='scheduler'){
						echo 'Asterisk died. contact your administrator!';
						}else{
						$already_meeting='asterisk error';
						}
					}else{
						if ($meeting_processor!='scheduler'){
						echo 'Click on the button bellow to start the meeting manually.<br />
						We\'ll try to connect to the following number : <b>'.$_SESSION['cong_phone_no'].'</b>'.$tmp_sip.'<br />';
						}
						//find a way to avoid having all the spaces in the strstr
						$sip_result2=str_replace(" ", "", $sip_result);
						if ($sip_caller_ip!=""){
							//$sip_result2, $_SESSION['cong_phone_no']."/".$_SESSION['cong_phone_no'].$sip_caller_ip
							//that doesnt work on debian or maybe it does to be tested
							if (strstr($sip_result2, $_SESSION['cong_phone_no'].$sip_caller_ip)){
								if ($meeting_processor!='scheduler'){
								echo '<b style="color:green;">The number seems to be reachable.</b><br /><br />';
								echo '<form action="" method="post">
								<input name="otp_time" type="hidden" value="'.time().'" />
								<input name="submit" id="input_login" type="submit" value="Start meeting" />
								</form>';
								}
							}else{
								if ($meeting_processor!='scheduler'){
								echo '<b style="color:red;">The number seems to be unreachable! The meeting won\'t start!<br />Please make sure that the Softphone (jitsy) is started OR restart the computer.</b><br /><br />';
								}else{
								$already_meeting='number unreachable';
								}
							}
						}else{
							//as we dont have an ip to compare to, we check that the phone no is unregistered
							// this breaks when asterisk died
							//$sip_result2, $_SESSION['cong_phone_no']."/".$_SESSION['cong_phone_no'].$tmp_unspec
							//that doesnt work on debian
							if (!strstr($sip_result2, $_SESSION['cong_phone_no'].$tmp_unspec)){
								if ($meeting_processor!='scheduler'){
								echo '<b style="color:green;">The number seems to be reachable.</b><br /><br />';
								echo '<form action="" method="post">
								<input name="otp_time" type="hidden" value="'.time().'" />
								<input name="submit" id="input_login" type="submit" value="Start meeting" />
								</form>';
								}
							}else{
								if ($meeting_processor!='scheduler'){
								echo '<b style="color:red;">The number seems to be unreachable! The meeting won\'t start!<br />Please make sure that the Softphone (jitsy) is started OR restart the computer.</b><br /><br />';
								}else{
								$already_meeting='number unreachable';
								}
							}
						}
					}
				}
			}
		}
?>