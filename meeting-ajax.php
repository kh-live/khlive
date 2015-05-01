<html>
<head>
<style type="text/css">
body{
	margin:0;
	padding:0;
	font-size:16px;
	color:black;
	font-family:arial,sans-serif;
	background-color:white;
}
#input_login{
	width:100px;
	margin-left:80px;
}
.live_user{
	width:190px;
	height:200px;
	float:left;
	z-index:100;
}
.stop{
	color:red;
}
#meeting_answer{
	position:fixed;
	top:0px;
	right:0px;
	background-color:#AAAAAA;
	border: 1px solid black;
	padding: 5px;
	width:450px;
}
#meeting_answer a,#meeting_answer-small a {
	color:white;
	text-decoration:none;
	font-weight:bolder;
	float:right;
	padding-left:50px;
}
.user_count{
height:30px;
margin-top:10px;
margin-left:10px;
position:absolute;
background-color:rgba(255,255,255,0.8);
border-radius:5px;
color: #eb691d;
padding:5px;
font-size:30px;
}
</style>
<script type="text/javascript">
function showdiv(d1, d2){
if(d1.length < 1) { return; }
if(d2.length < 1) { return; }
        document.getElementById(d1).style.display = "block";
        document.getElementById(d2).style.display = "none";
}
</script>
<?PHP
if(session_id()==""){session_start();}
date_default_timezone_set ('Africa/Johannesburg');
include "db/config.php";
//when stopping, it takes 23 secondes to hangup the call (or more on alpine linux)
if(strstr($_SESSION['meeting_status'],"live") AND @$_POST['submit']!="Yes, Stop it"){
 echo '<meta http-equiv="refresh" content=5>';
}else{
if (@$_POST['submit']!="Yes, Stop it"){
 echo '<meta http-equiv="refresh" content='.$timer.'>';
}
}
?>
</head>
<body>
<?PHP
if ($_SESSION['type']=="root" OR $_SESSION['type']=="admin" OR $_SESSION['type']=="manager"){
if ($server_beta!='true'){
$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
}
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$_SESSION['cong']) {
		$meeting_type=$data[5];
		$sip_caller_ip=@$data[13];
		}
	}
	
	if (strstr($_SESSION['meeting_status'],"live") OR $server_beta=='true'){ //for testing we trick it to believe it's live
	if(isset($_POST['submit'])){
	if($_POST['submit']=="Stop meeting"){
	echo 'Are you sure you want to stop the meeting ?<br /><br />
	<form action="" method="post">
	<input name="submit" id="input_login" type="submit" value="Cancel"><input name="submit" id="input_login" type="submit" value="Yes, Stop it">
	</form><br /><br />';
	}elseif($_POST['submit']=="Yes, Stop it"){
	$_SESSION['meeting_stop_time']=time();
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
		echo '<b style="color:red;">Please wait for the page to reload before doing anything else!</b><br />Stopping streams : Done <br />';
		//if the start time is not set we default to 2 hours
		if (!isset($_SESSION['meeting_start_time'])) $_SESSION['meeting_start_time']=time()-7200;
		$meeting_length=$_SESSION['meeting_stop_time'] - $_SESSION['meeting_start_time'];
		//26 is the no of seconds of recording encode by the server in one second on PI B+
		if (!isset($encoder_speed)) $encoder_speed=26;
		$time_to_encore = $meeting_length/$encoder_speed;
		echo ' Encoding Recording to MP3 : <b id="progress_percent">0%</b><br /><br /><progress id="progress_recording" value=0" max="100"></progress><br /><br />
		<script>
		var timeLeft='.$time_to_encore.';
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
		$_SESSION['meeting_just_stopped']=1;
			$info=time().'**info**meeting stop**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			}
	//find a way to wait for the phone call to finish before refreshing -> done with meta refresh
}elseif($_POST['submit']=="Disconnect"){
$client=$_POST['user'];
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($_SESSION['cong']==$data[0]) $cong_no=$data[1];
	}
exec($asterisk_bin.' -rx "meetme list '.$cong_no.'concise"',$conf_db);
		foreach ($conf_db as $line){
		$data=explode("!",$line);
		if (strstr($data[2],$client)) $kill=$data[3];
		}
	exec($asterisk_bin.' -rx "channel redirect '.$kill.' grg-meetme,killpin,1"');
		echo 'Disconnecting...<br /><br />';
}
}elseif(isset($_GET['kill'])){
	if ($_GET['kill']==1){
	echo 'Are you sure you want to disconnect the following user : '.$_GET['user'].' ?<br /><br />
	<form action="" method="post">
	<input name="user" type="hidden" value="'.$_GET['user'].'">
	<input name="submit" id="input_login" type="submit" value="Cancel"><input name="submit" id="input_login" type="submit" value="Disconnect">
	</form><br /><br />';
	}
}else{
		if (isset($_SESSION['meeting_just_started'])){
			if ($_SESSION['meeting_just_started']==1){
			$info=time().'**info**meeting start**'.$_SESSION['user']."**\n";
			$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			$_SESSION['meeting_just_started']='';
			echo '<b style="color:green;">The meeting was started successfuly!</b><br />';
			$_SESSION['meeting_start_time']=time();
			}
		}
		//failed to stop the meeting
		if (isset($_SESSION['meeting_just_stopped'])){
			if ($_SESSION['meeting_just_stopped']==1){
			$info=time().'**error**failure to stop the meeting**'.$_SESSION['user']."**\n";
			$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			$_SESSION['meeting_just_stopped']='';
			echo '<b style="color:red;">The meeting failed to stop!</b><br />';
			}
		}
	//not allowed to stop the meeting if it was started by the stream.
	if ($meeting_type!="none"){
	echo 'Use this button to stop the meeting<br /><br />
	<form action="" method="post">
	<input name="submit" id="input_login" type="submit" value="Stop meeting">
	</form><br /><br />';
	}
}
	
	echo '<b>Users live : </b><br /><br />';
	
	
	$db=file("db/live_users");
	if (count($db)==0){
	echo 'No live users';
	}else{
	foreach($db as $line){
	$data=explode ("**",$line);
	if($data[2]==$_SESSION['cong']){
	if($data[5]=="normal"){
	if($data[3]=="phone_live"){
	echo '<div class="live_user"><img src="./img/phone1.png" /><br />'.$data[0].' - <a class="stop" href="./meeting-ajax.php?kill=1&user='.$data[0].'">x</a></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	echo '<div class="live_user"><img src="./img/phone_record.png" /><br />'.$data[0].'</div>';
	}else{
	//this is streaming
	echo '<div class="live_user"><h1 class="user_count">'.$data[6].'</h1><img src="./img/comp1.png" /><br />'.$data[1].'</div>';
	}
	}elseif(strstr($data[5],"request")){
	if($data[3]=="phone_live"){
	echo '<div class="live_user"><a href="./answer.php?action=answering&client='.urlencode($data[0]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[1].'"><img src="./img/phone2.png" /></a><br />'.$data[0].'</div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	echo '<div class="live_user"><a href="./answer.php?action=sms_a&client='.$data[1].'&cong='.$data[2].'"><img src="./img/comp2.png" /></a><br />'.$data[1].' <br />(Answer to :'.urldecode($paragraph).')</div>';
	}
	}elseif(strstr($data[5],"answering")){
	if($data[3]=="phone_live"){
	echo '<div class="live_user"><a href="./answer.php?action=stop&client='.urlencode($data[0]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[1].'"><img src="./img/phone3.png" /></a><br />'.$data[0].'</div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	$answer=$tmp[2];
	echo '<div class="live_user"><img src="./img/comp3.png" /><div id="meeting_answer">
	<a href="./answer.php?action=sms_cancel&client='.$data[1].'&cong='.$data[2].'">NOT answered</a> <a href="./answer.php?action=sms_stop&client='.$data[1].'&cong='.$data[2].'">ANSWERED</a>
<b>ANSWER :<br />from : '.$data[1].'<br />to : '.$paragraph.'</b><br />'.urldecode($answer).'</div><br />'.$data[1].'</div>';
	}
	}else{
	//should not happen
	}
	}
	}
	}
	
	echo '</table>';

	}else{

$cong_name=$_SESSION['cong'];
if(isset($_POST['submit'])){
	if($_POST['submit']=="Start meeting"){
//start meeting
if ($meeting_type=="sip"){
	$info="Channel: SIP/".$_SESSION['cong_phone_no']."
MaxRetries: 1
RetryTime: 60
WaitTime: 30
Context: test-menu
Extension: meet_me_".$cong_name."_admin
Priority: 1
";
}elseif ($meeting_type=="iax"){
	$info="Channel: IAX2/".$_SESSION['cong_phone_no']."
MaxRetries: 1
RetryTime: 60
WaitTime: 30
Context: test-menu
Extension: meet_me_".$cong_name."_admin
Priority: 1
";
}elseif ($meeting_type=="direct"){
	$info="Channel: console/".$server_audio." 
MaxRetries: 1
RetryTime: 60
WaitTime: 30
Context: test-menu
Extension: meet_me_".$cong_name."_admin
Priority: 1
";
}
$file=fopen($asterisk_spool.'outgoing/meeting_'.$cong_name.'_admin.call','w');
			if(fputs($file,$info)){
			fclose($file);
			//fixit what if the call fails???
			//dont fill shm here it is done in the dialplan (line 142)
			//log accordingly
			/*$file=fopen('/dev/shm/meeting_'.$_SESSION['cong'],'w');
			fputs($file,"live");
			fclose($file);*/
			  echo 'Starting...<br /><br />';
			  $_SESSION['meeting_just_started']=1;
			$info=time().'**info**meeting start**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
	}
}
}else{
//if the meeting just stopped
if (isset($_SESSION['meeting_just_stopped'])){
			if ($_SESSION['meeting_just_stopped']==1){
			$info=time().'**info**meeting stop**'.$_SESSION['user']."**\n";
			$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			$_SESSION['meeting_just_stopped']='';
			echo '<b style="color:green;">The meeting was stopped successfuly!</b><br />';
			}
		}
// if the meeting failed to start
if (isset($_SESSION['meeting_just_started'])){
			if ($_SESSION['meeting_just_started']==1){
			$info=time().'**error**failure to start the meeting**'.$_SESSION['user']."**\n";
			$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
			$_SESSION['meeting_just_started']='';
			echo '<b style="color:red;">The meeting failed to start!</b><br />';
			}
		}
//otherwise
//aren't we doing that on line 79 already?
$db=file("db/cong");
foreach($db as $line){
$data=explode ("**",$line);
if ($data[0]==$_SESSION['cong']) {
$_SESSION['cong_phone_no']=$data[4];
$meeting_type=$data[5];
}
}
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
	echo 'Asterisk died. contact your administrator!';
	}elseif ($meeting_type=="direct"){
	echo 'Click on the button bellow to start the meeting.<br /><b style="color:green;">We\'ll try to connect to the server\'s sound card...</b><br /><br />';
	echo '<form action="" method="post">
	<input name="submit" id="input_login" type="submit" value="Start meeting">
	</form>';
	}else{
	echo 'Click on the button bellow to start the meeting.<br />
	We\'ll try to connect to the following number : <b>'.$_SESSION['cong_phone_no'].'</b>'.$tmp_sip.'<br />';
	//find a way to avoid having all the spaces in the strstr
	$sip_result2=str_replace(" ", "", $sip_result);
	if ($sip_caller_ip!=""){
	//$sip_result2, $_SESSION['cong_phone_no']."/".$_SESSION['cong_phone_no'].$sip_caller_ip
	//that doesnt work on debian or maybe it does to be tested
	if (strstr($sip_result2, $_SESSION['cong_phone_no'].$sip_caller_ip)){
	echo '<b style="color:green;">The number seems to be reachable.</b><br /><br />';
	echo '<form action="" method="post">
	<input name="submit" id="input_login" type="submit" value="Start meeting">
	</form>';
	}else{
	echo '<b style="color:red;">The number seems to be unreachable! The meeting won\'t start!<br />Please make sure that the Softphone (jitsy) is started OR restart the computer.</b><br /><br />';
	}
	}else{
	//as we dont have an ip to compare to, we check that the phone no is unregistered
	// this breaks when asterisk died
	//$sip_result2, $_SESSION['cong_phone_no']."/".$_SESSION['cong_phone_no'].$tmp_unspec
	//that doesnt work on debian
	if (!strstr($sip_result2, $_SESSION['cong_phone_no'].$tmp_unspec)){
	echo '<b style="color:green;">The number seems to be reachable.</b><br /><br />';
	echo '<form action="" method="post">
	<input name="submit" id="input_login" type="submit" value="Start meeting">
	</form>';
	}else{
	echo '<b style="color:red;">The number seems to be unreachable! The meeting won\'t start!<br />Please make sure that the Softphone (jitsy) is started OR restart the computer.</b><br /><br />';
	}
	}
	}
	}else{
	echo 'Press the "connect" button on Edcast to start the meeting.<br />The meeting wont be recorded on the server side.<br /> You have to record it yourself (with Audactiy).';
	}
	}
	}
	
	}
}
?>
</body>
</html>