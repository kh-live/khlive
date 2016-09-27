<?PHP
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
					//we need to include all the session info
					//$_SESSION['type']="manager";
					//$_SESSION['cong']=$cong;
					//$_POST['submit']=="Start meeting"
					//we need to make sure that there is no other cong having a meeting already
					
					//we include the ajax file
					
					$info=time().'**info**schedule meeting launched successful**'.$cong."**\n";
					$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
					if(fputs($file,$info)){
						fclose($file);
					}
				}elseif (($enabled=='yes') AND (date('D',time())==$day) AND (date('G',time())==$stop_time[0]) AND ( (1*date('i',time())==$stop_time[1]) OR ((1*date('i',time())) +1 == $stop_time[1]) OR ((1*date('i',time())) -1 == $stop_time[1]) ) ){
					//we need to include all the session info
					//$_SESSION['type']="manager";
					//$_SESSION['cong']=$cong;
					//$_POST['submit']=="Yes, Stop it"
					//we need to make sure that there is still a meeting to stop
					
					//we include the ajax file
					
					$info=time().'**info**schedule meeting stopped successful**'.$cong."**\n";
					$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
					if(fputs($file,$info)){
						fclose($file);
					}
				}
			}
		}
	
	}
}
?>