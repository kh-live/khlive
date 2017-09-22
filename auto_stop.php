<?PHP
if(session_id()==""){session_start();}
date_default_timezone_set ('Africa/Johannesburg');
include 'db/config.php';
if (isset($auto_stop)){
	if ($auto_stop=='yes'){
	chdir(dirname(__FILE__));

					//we need to set all the required variables

					$_SESSION['user']='auto_stop';
					$db=file("db/cong");
					foreach($db as $line){
						$data=explode ("**",$line);
						$_SESSION['cong']=$data[0];
						$cong=$data[0];
							$meeting_type=$data[5];
							$sip_caller_ip=@$data[13];
							$record=$data[10];
							$stream_quality=$data[12];
							$bitrate=15+(3*$stream_quality);
							$stream_type=$data[7];
							$_SESSION['cong_phone_no']=$data[4];
						
					
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
					
					if (strstr($_SESSION['meeting_status'],"live") OR $_SESSION['test_meeting_status']=="live"){
						//we include the stop file
						$meeting_processor='scheduler';
						include 'meeting-stop.php';
						$info=time().'**info**meeting auto_stop successful**'.$cong."**\n";
						$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
						if(fputs($file,$info)){
							fclose($file);
						}
					}

				}
			
	}

}

?>