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
//this is not set until later...
if(@$_SESSION['meeting_just_stopped']==1){
//meeting finished we don't refresh
}elseif(strstr($_SESSION['meeting_status'],"live") AND @$_POST['submit']!="Yes, Stop it"){
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
		$record=$data[10];
		$stream_quality=$data[12];
		$bitrate=15+(3*$stream_quality);
		$stream_type=$data[7];
		$_SESSION['cong_phone_no']=$data[4];
		
		}
	}
/*first everything that happens when the meeting is live*/	
	if (strstr($_SESSION['meeting_status'],"live") OR $server_beta=='true'){ //for testing we trick it to believe it's live
	
if(isset($_POST['submit'])){
	if($_POST['submit']=="Stop meeting"){
		echo 'Are you sure you want to stop the meeting ?<br /><br />
		<form action="" method="post">
		<input name="submit" id="input_login" type="submit" value="Cancel"><input name="submit" id="input_login" type="submit" value="Yes, Stop it">
		</form><br /><br />';
	}elseif($_POST['submit']=="Yes, Stop it"){
		$meeting_processor='ajax';
		include 'meeting-stop.php';
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
			//we must move that to dev/shm otherwise we'll loose the info when session is closed
			$_SESSION['meeting_start_time']=time();
			echo '<script>
		setTimeout(function(){ window.location= "./meeting-ajax.php"},5000);
		</script>';
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
		if (@$_SESSION['test_meeting_status']!="live"){
			echo 'Use this button to stop the meeting<br /><br />
			<form action="" method="post">
			<input name="submit" id="input_login" type="submit" value="Stop meeting">
			</form><br /><br />';
		}else{
			echo '<b style="color:orange;">This is a test meeting.</b><br />Don\'t forget to stop it using the button on diagnosis page.<br /><br />';
		}
	}
}
/*we display the listeners list */
include 'meeting-listeners.php';
/*then everything that happens when the meeting isnt live */
}else{
	if(isset($_POST['submit'])){
		if($_POST['submit']=="Start meeting"){
		//start meeting
		$meeting_processor='ajax';
		include 'meeting-start.php';
		}
	}else{
		//if the meeting just stopped
		$skip=0;
		if (isset($_SESSION['meeting_just_stopped'])){
			if ($_SESSION['meeting_just_stopped']==1){
				$info=time().'**info**meeting stop**'.$_SESSION['user']."**\n";
				$file=fopen('./db/logs-'.strtolower($_SESSION['cong']).'-'.date("Y",time()).'-'.date("m",time()),'a');
				if(fputs($file,$info)){
					fclose($file);
				}
				$_SESSION['meeting_just_stopped']='';
				$skip=1;
				echo '<b style="color:green;">The meeting was stopped successfuly!</b><br />Click <a href="./meeting-ajax.php">here</a> if you want to start the meeting again.<br /><br /><br />';			
				$tmp_results=array();
				if ($dh = @opendir("./records")) {
					while (($file = readdir($dh)) !== false) {
						if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 
							if (strstr($file,$_SESSION['cong'])) {
								$tmp_results[]=$file;
							}
						}
					}
					closedir($dh);
				}
				rsort($tmp_results);
				$file=$tmp_results[0];
				$info=filesize("./records/".$file);
				if ($info>=1048576){
					$info=round($info/1048576,1);
					$info.=" MB";
				}elseif($info>=1024){
					$info=round($info/1024,1);
					$info.=" kB";
				}else{
					$info.=" B";
				}
				echo 'Right click on the following link to save the latest meeting : <br /><br /><a href="./download.php?file='.$file.'" download>'.$file.'</a><br /><br /> (Size : '.$info.')';
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
		if (($meeting_type=="direct" OR $meeting_type=='direct-stream')){
			if ($skip==0){
				echo 'Click on the button bellow to start the meeting.<br /><b style="color:green;">We\'ll try to connect to the server\'s sound card...</b><br /><br />';
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
											$already_meeting=$file;
										}
									}
								}
							}
						}
						closedir($dh);
					}
				}
				if ($already_meeting==''){
					echo '<form action="" method="post">
					<input name="submit" id="input_login" type="submit" value="Start meeting">
					</form>';
				}else{
					echo '<b style="color:red;">there is already a meeting on this server started by : '.$already_meeting.'</b><br />Terminate that one first before you can start yours.<br />';
				}
			}
		}elseif ($meeting_type=='none'){
			echo 'Press the "connect" button on Edcast to start the meeting.<br />The meeting wont be recorded on the server side.<br /> You have to record it yourself (with Audactiy).';
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
						echo 'Asterisk died. contact your administrator!';
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
				}
			}
		}
	}
}
}
?>
</body>
</html>