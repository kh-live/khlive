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
				$info=time().'**info**schedule meeting launched successful**'.$cong."**\n";
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