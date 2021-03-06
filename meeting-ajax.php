<html>
<head>
<style type="text/css">
body{
	margin:0;
	padding:10px;
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
	width:100%;
	max-width:450px;
	min-height:90px;
	box-sizing: border-box;
	z-index:100;
	box-shadow: 0 3px 3px rgba(0, 0, 0, 0.19);
	margin-bottom:15px;
}
.live_user_1{
	width:100%;
	max-width:450px;
	box-sizing: border-box;
	min-height:90px;
	background-color:#f0f4f8;
	z-index:100;
	box-shadow: 0 3px 3px rgba(0, 0, 0, 0.19);
	margin-bottom:15px;
}
.live_user_name{
    margin-left: 100px;
    margin-top: -80px;
    position: relative;
    min-height:70px;
    padding-bottom:10px;
}
.live_user_link {
display:inline-block;
    width:100%;
    min-height:90px;
    text-decoration:none;
    color:black;
}
.live_user img, .live_user_1 img{
width:90px;
height:90px;
}
.stop{
	color:red;
}
.meeting_answer{
	padding: 5px;
	width:100%;
	box-sizing: border-box;
}
.meeting_answer a{
background-color: #61b131;
    color: white;
    display: inline-block;
    font-weight: bolder;
    line-height: 30px;
    margin: 5px;
    padding:5px;
    text-align: center;
    text-decoration: none;
    width: 120px;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.19);
}
.meeting_answer a:hover{
text-decoration:underline;
}
.user_count{
line-height:30px;
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
if ( $server_beta=='true'){
//we're in testing mode
echo '</head>
<body>';
include 'meeting-listeners.php';
}else{
//when stopping, it takes 23 secondes to hangup the call (or more on alpine linux)
//this is not set until later...
if(@$_SESSION['meeting_just_stopped']==1){
//meeting finished we don't refresh
}elseif(strstr($_SESSION['meeting_status'],"live") AND @$_POST['submit']!="Yes, Stop it"){
//refresh during the meeting.
 echo '<meta http-equiv="refresh" content=5>';
}else{
if (@$_POST['submit']!="Yes, Stop it") {
//refresh before the meeting starts
 echo '<meta http-equiv="refresh" content='.$timer.'>';
}
}
?>
</head>
<body>
<?PHP
if ($_SESSION['type']=="root" OR $_SESSION['type']=="admin" OR $_SESSION['type']=="manager"){
if (is_file($temp_dir.'meeting_'.$_SESSION['cong'])){
$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
}else{
$_SESSION['meeting_status']='down';
}

	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$_SESSION['cong']) {
		$meeting_type=$data[5];
		$sip_caller_ip=@$data[13];
		$record=$data[10];
		$stream_quality=$data[12];
		$bitrates=array(
		'8000-0' => '8',
		'8000-1' => '16',
		'8000-2' => '24',
		'8000-3' => '32',
		'8000-4' => '40',
		'8000-5' => '48',
		'8000-6' => '56',
		'8000-7' => '64',
		'8000-8' => '64',
		'8000-9' => '64',
		'8000-10' => '64',
		'11025-0' => '8',
		'11025-1' => '16',
		'11025-2' => '24',
		'11025-3' => '32',
		'11025-4' => '40',
		'11025-5' => '48',
		'11025-6' => '56',
		'11025-7' => '64',
		'11025-8' => '64',
		'11025-9' => '64',
		'11025-10' => '64',
		'16000-0' => '16',
		'16000-1' => '24',
		'16000-2' => '32',
		'16000-3' => '40',
		'16000-4' => '48',
		'16000-5' => '56',
		'16000-6' => '64',
		'16000-7' => '80',
		'16000-8' => '96',
		'16000-9' => '112',
		'16000-10' => '128',
		'22050-0' => '24',
		'22050-1' => '32',
		'22050-2' => '40',
		'22050-3' => '48',
		'22050-4' => '56',
		'22050-5' => '64',
		'22050-6' => '80',
		'22050-7' => '96',
		'22050-8' => '112',
		'22050-9' => '128',
		'22050-10' => '144',
		'32000-0' => '32',
		'32000-1' => '40',
		'32000-2' => '48',
		'32000-3' => '56',
		'32000-4' => '64',
		'32000-5' => '80',
		'32000-6' => '96',
		'32000-7' => '112',
		'32000-8' => '128',
		'32000-9' => '160',
		'32000-10' => '192',
		'44100-0' => '32',
		'44100-1' => '40',
		'44100-2' => '48',
		'44100-3' => '56',
		'44100-4' => '64',
		'44100-5' => '80',
		'44100-6' => '96',
		'44100-7' => '112',
		'44100-8' => '128',
		'44100-9' => '160',
		'44100-10' => '192',
		'48000-0' => '32',
		'48000-1' => '40',
		'48000-2' => '48',
		'48000-3' => '56',
		'48000-4' => '64',
		'48000-5' => '80',
		'48000-6' => '96',
		'48000-7' => '112',
		'48000-8' => '128',
		'48000-9' => '160',
		'48000-10' => '192'
		);
		$bitrate= $bitrates[$sound_quality.'-'.$stream_quality]; //round((15+(2*$stream_quality)) * ($sound_quality/8000));
		/*
		MPEG-1   layer III sample frequencies (kHz):  32  48  44.1
bitrates (kbps): 32 40 48 56 64 80 96 112 128 160 192 224 256 320

MPEG-2   layer III sample frequencies (kHz):  16  24  22.05
bitrates (kbps):  8 16 24 32 40 48 56 64 80 96 112 128 144 160

MPEG-2.5 layer III sample frequencies (kHz):   8  12  11.025
bitrates (kbps):  8 16 24 32 40 48 56 64
*/
		$stream_type=$data[7];
		$_SESSION['cong_phone_no']=$data[4];
		
		}
	}
/*first everything that happens when the meeting is live*/	
	if (strstr($_SESSION['meeting_status'],"live") ){ 
	
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
	}elseif($_POST['submit']=="Cancel Auto Stop"){
		echo 'Are you sure you want to cancel automatic stop of the meeting ?<br />
		If you do so, you will have to stop the meeting manually!<br />
		(the bypass is active for 10 hours)<br />
		<form action="" method="post">
		<input name="submit" id="input_login" type="submit" value="No"><input name="submit" id="input_login" type="submit" value="Yes, Cancel it">
		</form><br /><br />';
	}elseif($_POST['submit']=="Yes, Cancel it"){
		echo 'Bypassing...<br /><br />';
		$now=time();
		$file=fopen($temp_dir.'bypass_'.$_SESSION['cong'],'w');
		fputs($file,$now);
		fclose($file);
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

			$file=fopen($temp_dir.'start_'.$_SESSION['cong'],'w');
			fputs($file,time());
			fclose($file);
			
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
			echo 'Use this button to stop the meeting manually<br /><br />
			<form action="" method="post">
			<input name="submit" id="input_login" type="submit" value="Stop meeting">
			</form><br /><br />';
			//we must add a bypass button in case the meeting is to be stopped automatically but it's not finished yet.
			if (@$scheduler=='yes'){
				if (file_exists($temp_dir.'stop_'.$_SESSION['cong'])){
					$stop_time=implode("",file($temp_dir.'stop_'.$_SESSION['cong']));
					$time_left= $stop_time - time();
					$bypass_time=0;
					if (file_exists($temp_dir.'bypass_'.$_SESSION['cong'])){
						$bypass_time=implode("",file($temp_dir.'bypass_'.$_SESSION['cong']));
					}
					$time_since_bypass= time() - $bypass_time ;
					if ($time_left >= 0 AND $time_left <= (5*60) AND $time_since_bypass >= (10*60*60)){
						echo '<i style="padding:10px;background-color:yellow;color:black;display:block;">NOTE : The meeting will be automatically be stopped in '.$time_left.' sec. <br />
						Press on the button below to disable automatic stopping :<br /><br />
						<form action="" method="post">
						<input style="width:300px;" name="submit" type="submit" value="Cancel Auto Stop">
						</form></i>';
					}elseif( $time_since_bypass <= (10*60*60)){
					echo '<i style="padding:10px;background-color:yellow;color:black;display:block;">The Auto Stop is bypassed. Dont forget to stop the meeting manually!
						</i><br /><br />';
					}
				}
			}
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
			//we make sure the request was made within two minutes otherwise it could be an old request recycled by apache
			if (((time() - 120) <= $_POST['otp_time']) AND (  $_POST['otp_time'] <= (time() + 120))){
		//start meeting
		$meeting_processor='ajax';
		include 'meeting-start.php';
			}
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
				echo '<b style="color:green;">The meeting was stopped successfuly!</b><br />
				Click <a href="./meeting-ajax.php">here</a> if you want to start the meeting again.<br /><br /><br />';			
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
				echo 'Right click on the following link to save the latest meeting : <br />
				<br /><a href="./download.php?file='.$file.'" download>'.$file.'</a><br /><br /> (Size : '.$info.')<br /><br />';
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
			//we need to set special access rights to the file for this to work
			echo 'Here is the last 10 lines of the error log : <br />';
			exec('tail -10 /var/log/apache2/error.log', $output);
			echo implode('<br />',$output);
			}
		}
		//otherwise  we do prechecks before we show the form to start meeting
		$meeting_processor='ajax';
		include 'meeting-precheck.php';
	}
}
}
}
?>
</body>
</html>