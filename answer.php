<?PHP
/*this page can be accessed by ajax call from the listen page then it returns something to the browser. Or through a get call then it doesnt return anything.
there seems to be quite some mess in this file. check where the actions come from and see if they all are necessary
there are 3 states of answering - normal - request - answering*/
include_once "db/config.php";

 if (isset($_REQUEST['action'])){
	if ($_REQUEST['action']=="request"){
	$action="request";//coming from?? - that's a request over the phone_live
	}elseif ($_REQUEST['action']=="cancel"){
	$action="normal";
	}elseif ($_REQUEST['action']=="answering"){
	$action="answering";
	}elseif ($_REQUEST['action']=="stop"){
	$action="normal";
	}elseif ($_REQUEST['action']=="sms"){
	$action="request--".$_POST['paragraph']."--".str_replace(array("\r\n", "\r", "\n"), "<br />", htmlentities($_POST['answer']));
	}elseif ($_REQUEST['action']=="sms_a"){
	$action="answering";
	}elseif ($_REQUEST['action']=="sms_stop"){
	$action="normal";
	}elseif ($_REQUEST['action']=="sms_cancel"){
	$action="normal";
	}
	if(isset($action)){
	$khuid='';
	$cong=$_REQUEST['cong'];
	$client=$_REQUEST['client'];
	$type=@$_GET['type']; //useless... no - setwhen first answering over phone = phone_live
	$conf=@$_GET['conf'];
	$khuid=@$_REQUEST['khuid'];
	
	$ij=0;
	$db=file("db/live_users");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if (($khuid=='' AND $data[0]==$client) OR ($khuid==$data[7])){//client is actually the id of stream -- what if one client has 2 streams???
		//add a check to see if the answer has been read
			if ($_REQUEST['action']=="sms_a"){
			$tmp=explode("--",$data[5]);
			$action="answering--".$tmp[1]."--".$tmp[2];
			}
		$file_content.=$data[0].'**'.$data[1].'**'.$data[2].'**'.$data[3].'**'.$data[4].'**'.$action.'**'.$data[6]."**".$data[7]."**".$data[8]."**\n";//here is the action used to be passed to the meeting-ajax.php
		$ij++;
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/live_users','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}
	if ($ij<=0){
	//This is also called from meeting.php when there is two users with the same name.
	if (isset($_GET['ajax_meeting_page'])){
		if ($_GET['ajax_meeting_page']=='ok'){
		header('Location: ./meeting-ajax.php');
		}		
		}else{
	echo '<div id="error">Error! It seems you\'re not listening to the meeting. Press play above or refresh the page. Then you\'ll be able to answer.</div>';
	exit;
	}
	}
	if($action=="answering"){
	exec($asterisk_bin.' -rx "meetme list '.$conf.' concise"',$conf_db);	
	foreach ($conf_db as $line){
		$data=explode("!",$line);
		if (strstr($client,$data[2]) OR strstr($data[3],$client)) $unmute=$data[0];//data2 works on alpine data3 on rpi...
		}
	exec($asterisk_bin.' -rx "meetme unmute '.$conf.' '.$unmute.'"');
	$info=time().'**info**answer start**'.$client."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	header('Location: ./meeting-ajax.php'); 
	}elseif ($_REQUEST['action']=="stop"){
	exec($asterisk_bin.' -rx "meetme list '.$conf.' concise"',$conf_db);
		foreach ($conf_db as $line){
		$data=explode("!",$line);
		if (strstr($client,$data[2]) OR strstr($data[3],$client)) $mute=$data[0];
		}
	exec($asterisk_bin.' -rx "meetme mute '.$conf.' '.$mute.'"');
	$info=time().'**info**answer stop**'.$client."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	header('Location: ./meeting-ajax.php');
	}elseif($_REQUEST['action']=="sms"){
	//this comes trough a javascript ajax
	$file_content=time()."**".$cong."**".$client."**".$_POST['paragraph']."**".str_replace(array("\r\n", "\r", "\n"), "<br />", htmlentities($_POST['answer']))."**new**".$khuid."**\n";
	$file=fopen('./db/answers','a');
			if(fputs($file,$file_content)){
			fclose($file);
			echo "success";
			}
			$info=time().'**info**answer start**'.$client."**txt**\n";
			$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}elseif($_REQUEST['action']=="sms_a"){
	/*this never happens! because action is set to answering at beggining - it actually does happen... weird*/
	$file_content=time()."**".$cong."**".$client."**read**".$khuid."**\n";
	$file=fopen('./db/answers','a');
			if(fputs($file,$file_content)){
			fclose($file);
			}
		header('Location: ./meeting-ajax.php');
	}elseif($_REQUEST['action']=="sms_stop"){
	$file_content=time()."**".$cong."**".$client."**done**".$khuid."**\n";
	$file=fopen('./db/answers','a');
			if(fputs($file,$file_content)){
			fclose($file);
			}
			$info=time().'**info**answer stop**'.$client."**ok**".$khuid."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
		header('Location: ./meeting-ajax.php');
	}elseif($_REQUEST['action']=="sms_cancel"){
	$file_content=time()."**".$cong."**".$client."**canceled**".$khuid."**\n";
	$file=fopen('./db/answers','a');
			if(fputs($file,$file_content)){
			fclose($file);
			}
			$info=time().'**info**answer stop**'.$client."**ko**".$khuid."**\n";
	$file=fopen('./db/logs-'.strtolower($cong).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			//if the cancellation comes from the user we must not redirect
			if (isset($_REQUEST['agent'])){
				if ($_REQUEST['agent']=="client") echo "done";
			}else{
		header('Location: ./meeting-ajax.php');
		}
	}
}
        
}
?>
