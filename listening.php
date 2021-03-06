<?PHP
//this page is either included or contacted directly in an iframe (for remote connection)
$print='ok';


$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
//we're accessing the page through an iframe
$a = session_id();
if ($a == ''){
session_start();
}
include "db/config.php";
include "lang.php";
//in case of i frame we need to set some session variables that we get through a get arg
$_SESSION['user']=$_GET['user'];
$_SESSION['cong']=$_GET['cong'];
//khuid is a unique identifier set on the client as we can't reliably pass the edcast id back to the listener
$_SESSION['khuid']=$_GET['khuid'];
if (isset($_GET['multi'])) $_SESSION['type']='multi';
$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']=="on"){
		$proto='https://';
		}else{
		$proto='http://';
		}
echo '<html><head>
<style type="text/css">
body {
    color: black;
    font-family: sans-serif;
    font-size: 16px;
    margin: 0;
    overflow-x: hidden;
}
audio {
	width:100%;
	box-sizing: border-box;
	padding: 0 10px;
}
#page {
    background-color: white;
    overflow-y: auto;
    padding: 10px 10px 30px;
}

#feeds {
    padding-left: 10px;
}
#feeds u {
    color: red;
    text-decoration: none;
}
#number_at, #multiusers {
    background-color: grey;
    color: white;
    font-size: 20px;
    margin-top: 20px;
    min-height: 30px;
    padding: 10px;
    text-align: center;
}
#sms_small {
    background-color: grey;
    color: white;
    font-size: 20px;
    margin-top: 20px;
    min-height: 30px;
    padding: 10px;
    text-align: center;
}
#sms {
    background-color: #eeeeee;
    border: 1px solid grey;
    display: none;
    margin-top: 30px;
    padding: 10px;
    width: 100%;
    box-sizing:border-box;
}
#p_no{
	width:100%;
	box-sizing: border-box;
}
#answer{
	width:100%;
	height:70px;
	box-sizing: border-box;
}
@font-face{
	font-family: test1;
	src:url(\'./fonts/digital-7-mono.ttf\');
}
@font-face{
	font-family: test2;
	src:url(\'./fonts/Orbitron-Black.ttf\');
}
#meeting_time{
	font-family: \'test2\', sans-serif;
	top:40px;
	left:0;
	width:100%;
	background-color: black;
	margin:0;
	color:white;
	text-align: center;
}
#meeting_overall{
	background-color:black;
display:inline-block;
	padding:15px;
	text-align:center;
	font-size:0.8em;
}
#meeting_times, #meeting_clock{
	font-family: \'test1\', sans-serif;
	text-align:center;
	font-size:1em;
	display:inline-block;
	padding-left:30px;
	padding-right:30px;
}
#hours, #minutes, #secondes, #meeting_clock h1{
display:inline-block;
}
 #meeting_clock{
display:none;
 }
</style>
</head>
<body>';
}else{
//we're serving the base page
//we need to set the khuid to be able to identify who is reporting attendance and who is answering in case the same username is used by different people
if (!isset($_SESSION['khuid'])){
	$_SESSION['khuid']=md5(time().rand(1000,1000000000));
}
//if it's from the master server, we need to redirect to the slave server
if ($server_beta=="master"){
$print='ko';
$url="";
$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$url=$data[0];
	$ip=$data[1];
	//we need to check if SSL is enabled and which port to use
	$s_enable_ssl=@$data[5];
	$s_http_port=@$data[6];
	$s_https_port=@$data[7];
	}
	}
if ($url==""){
echo '<div id="page"><h2>'.$lng['listening'].'</h2><br /><br />Could not find your congregations server...</div>';
}else{
//we check if the meeting is live or not
if ($_SESSION['meeting_status']=='live'){
	if ($s_enable_ssl=='force'){
		$url='https://'.$url.':'.$s_https_port;
	}elseif($s_enable_ssl=='no' OR $s_enable_ssl==''){
		if ($s_http_port!='') $s_http_port=':'.$s_http_port;
		$url='http://'.$ip.$s_http_port;
	}else{
	//this is auto
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']=="on"){
		$url='https://'.$url.':'.$s_https_port;
		}else{
		if ($s_http_port!='') $s_http_port=':'.$s_http_port;
		$url='http://'.$ip.$s_http_port;
		}
	}
$addparam='';
if ($_SESSION['type']=='multi') $addparam='&multi=1';
echo '<div id="page"><h2>'.$lng['listening'].'</h2><iframe id="listen_frame" src="'.$url.'/kh-live/listening.php?user='.$_SESSION['user'].'&cong='.$_SESSION['cong'].'&khuid='.$_SESSION['khuid'].$addparam.'"></iframe></div>';
}else{
echo '<div id="page"><h2>'.$lng['listening'].'</h2><br /><br /><div id="feeds">'.$lng['nolive'].' :<br /><br /><u>'.$lng['not_available'].'</u><br /><br /></div>'.$lng['listen_records'].'<br /></div>';
}
}
}else{
//on slave server page accessed not through iframe
echo '<div id="page"><h2>'.$lng['listening'].'</h2>';
}
}
if ($print=='ok'){
?>
<script type="text/javascript">
var count="";
function update_at_no(no){
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    resp=xmlhttp.responseText;
    if (resp=="ok"){
//everything went fine
    }else{
	alert("There was an error while updating the attendance. Please try again.");
    }
   
    }
  }
xmlhttp.open("GET","listener_joined.php?action=update_at&user=<?PHP echo $_SESSION['user'];?>&khuid=<?PHP echo $_SESSION['khuid'];?>&cong=<?PHP echo $_SESSION['cong'];?>&number=" + no, true);
xmlhttp.send();
}
function cancel_answer(){
var r=confirm("Are you sure you want to cancel your answer ?");
if (r==true)
  {
  
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    resp=xmlhttp.responseText;
    if (resp=="done"){
//everything went fine
document.getElementById("sms_small").onclick="";
document.getElementById("sms_small").style.cursor= "auto";
	document.getElementById("sms_small").onmouseover = function() {
    this.style.textDecoration = "none";
    }
	 document.getElementById("sms_small").innerHTML="Your answer is canceled !<br />";
	 var tmp=counter(1);
    }else{
	alert("There was an error while canceling the answer. Please try again.");
    }
   
    }
  }
xmlhttp.open("GET","answer.php?action=sms_cancel&agent=client&client=<?PHP echo $_SESSION['user'];?>&khuid=<?PHP echo $_SESSION['khuid'];?>&cong=<?PHP echo $_SESSION['cong'];?>", true);
xmlhttp.send();
}
}
function showdiv(d1, d2){
if(d1.length < 1) { return; }
if(d2.length < 1) { return; }
        document.getElementById(d1).style.display = "block";
        document.getElementById(d2).style.display = "none";
	alert("IMPORTANT! Note that the sound that you are hearing now is delayed by 30seconds at least. Send your answer well in advance!");
}
function send_answer(){
var paragraph = encodeURIComponent(document.getElementById("p_no").value) ;
var answer = encodeURIComponent(document.getElementById("answer").value) ;
if(paragraph.length < 1) { 
alert("Please complete the paragraph no");
}else if(answer.length < 1) { 
alert("Please write your answer");
}else{

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    resp=xmlhttp.responseText;
    
    if (resp=="success"){
        document.getElementById("sms_small").style.display = "block";
	document.getElementById("sms").style.display = "none";
	document.getElementById("p_no").value="" ;
	document.getElementById("answer").value="" ;
    trackSms("<?PHP echo $_SESSION['user'];?>","<?PHP echo $_SESSION['cong'];?>");
    }else{
    document.getElementById("sms").innerHTML="Error :" + resp + "<br />" + document.getElementById("sms").innerHTML;
    }
    }
  }
xmlhttp.open("POST","answer.php", true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("action=sms&client=<?PHP echo $_SESSION['user'];?>&khuid=<?PHP echo $_SESSION['khuid'];?>&cong=<?PHP echo $_SESSION['cong'];?>&paragraph=" + paragraph + "&answer=" + answer);
}
}
function trackSms(user,cong){
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    resp=xmlhttp.responseText;
    if (resp=="received" || resp=="waiting"){
	//add event to check in 30sec
	var timer=setTimeout(function(){trackSms("<?PHP echo $_SESSION['user'];?>","<?PHP echo $_SESSION['cong'];?>")}, 30000);
	//deactivate onclick
	document.getElementById("sms_small").onclick=function () {cancel_answer();}

	 document.getElementById("sms_small").innerHTML='Your answer is : ' + resp + '<br /> If you do not want to answer anymore, click here to cancel your answer!<br />';
	 var tmp=counter(1);
    }else{
    //activate onclick showdiv('sms','sms_small')
	option =
	document.getElementById("sms_small").style.cursor= "pointer";
	document.getElementById("sms_small").onmouseover = function() {
    this.style.textDecoration = "underline";
    }
    document.getElementById("sms_small").onmouseleave = function() {
    this.style.textDecoration="none";
    }
	document.getElementById("sms_small").onclick=function () {showdiv('sms','sms_small');}
	clearTimeout(count);
	 document.getElementById("sms_small").innerHTML="Your answer is : " + resp + " ! Click here to answer again.";
    }
   
    }
  }
xmlhttp.open("GET","sms_check.php?usr=<?PHP echo $_SESSION['user'];?>&khuid=<?PHP echo $_SESSION['khuid'];?>&cong=<?PHP echo $_SESSION['cong'];?>", true);
xmlhttp.send();
}
function counter (i){
 document.getElementById("sms_small").innerHTML=document.getElementById("sms_small").innerHTML + "."
 if (i<=9){
  count=setTimeout(function(){counter(i+1)}, 3000);
 }
}
</script>
<?PHP
$db0=file("db/cong");
    foreach($db0 as $line){
    $data=explode ("**",$line);
	if ($data[0]==$_SESSION['cong']){
	$meeting_type=$data[5];
}
}
if (@$timing_conf=='yes' AND ($meeting_type!="none")){
?>
<script type="text/javascript">
function refreshPage(){
//we need to make a ajax call to meeting-time.php and update the content
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttpTime=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttpTime=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttpTime.onreadystatechange=function()
  {
  if (xmlhttpTime.readyState==4 && xmlhttpTime.status==200)
    {
    resp=xmlhttpTime.responseText;

    MyDiv=document.getElementById('timing_container');
    MyDiv.innerHTML=resp;
   var arr = MyDiv.getElementsByTagName('script');
for (var n = 0; n < arr.length; n++) {
    eval(arr[n].innerHTML);
    }
    }
  }
      //clearInterval(window.KhClock);
tstmp = new Date();
xmlhttpTime.open("GET","./meeting-time.php?tmp=" +  tstmp.getTime(), true);
xmlhttpTime.send();
}
</script>
<div id="timing_container">
<?PHP
include 'meeting-time.php';
?>
</div>
<?PHP } 
	if (@$scheduler=='yes' AND ($meeting_type!="none")){
			//we display when the next schedulded meeting is going to take palce
			echo '<br /><i style="background-color:rgba(0,0,0,0.3);display:block;">Scheduled meetings :<br />';
			$smeetings='';
			if (is_file('./db/sched')){
			$db0=file('./db/sched');
			if ($db0!=''){
			foreach($db0 as $line){
				$data=explode('**', $line);
				$cong=$data[0];
				$day=$data[1];
				$start_time=explode(':',$data[2]);
				if ($start_time[1]=='0') $data[2]=$data[2].'0';
				$stop_time=explode(':',$data[3]);
				if ($stop_time[1]=='0') $data[3]=$data[3].'0';
				$enabled=$data[4];
				if (($enabled=='yes') AND (date('D',time())==$day) AND $_SESSION['cong']==$cong){
					$smeetings.= '- Meeting from '.$data[2].' to '.$data[3].' </br>';
				}
				}
			}
			}
			if ($smeetings!=''){
			echo $smeetings;
			}else{
			echo 'No meeting scheduled for today for your congregation';
			}
			echo '</i><br />';
			//countdown
				if ($smeetings!=''){
					$temp_meetings=explode ('</br>', $smeetings);
					foreach($temp_meetings as $temp_meeting){
					
							$temp_start=explode(' to ',$temp_meeting);
							$temp_start2=explode('from ',$temp_start[0]);
							$temp_start3=explode(':',@$temp_start2[1]);
							$temp_hour=$temp_start3[0];
							$temp_min=@$temp_start3[1];
							
							$today=date('d.m.Y', time());
							$start_timestamp=strtotime($today." ".@$temp_start2[1]);
							if ($start_timestamp > time()){
								$time_min_left=((($start_timestamp-time())/60) % 60);
								$time_hour_left=(($start_timestamp-time())/3600) % 24;
								if ($time_hour_left == 0 AND $time_min_left == 0) {
									echo 'Starting...<br />Please wait 60 seconds for the page to refresh.<br /><br />';
								}else{
									echo 'The meeting will start in : ';
								
									if ($time_hour_left > 0) echo $time_hour_left.' h and ';
									if ($time_min_left >= 0) echo $time_min_left.' min<br /><br />';
								}
							}
						
					}
				}
			}

			
echo $lng['listening_text'].'<br /><br />';

?>
<div id="feeds">
<?PHP	
	$db=file("db/streams");
	$live=file("db/live_streams");

	$buffer="";
    foreach($db as $line){
    $data=explode ("**",$line);
	if ($data[1]==$_SESSION['cong']){

	$feeds[]=$data[0];
	$types[]=$data[2];
}
}

	if(strstr($_SESSION['meeting_status'],"live") OR $server_beta=='true'){
	echo $lng['yeslive'].' :<br /><br />';
	
	if ($meeting_type=='jitsi'){
	include('listening-jitsi.php');
	}else{
    
    if (strstr($_SERVER['HTTP_HOST'],'192.168.')){
    $server_out=$_SERVER['HTTP_HOST'];
    }
    if ($proto=='https://') $port=$icecast_ssl_port;

    $buffer.='<audio controls autoplay>';
    $i=0;
    foreach ($feeds as $feed){
    $type=$types[$i];
	$type_txt="";
    if ($type=="mp3") $type_txt="audio/mpeg";
    if ($type=="ogg") $type_txt="audio/ogg";
    //warning the order of param is important
    $buffer.='<source src="'.$proto.$server_out.':'.$port.$feed.'?user='.$_SESSION['user'].'&pass='.$_SESSION['cong'].'&khuid='.$_SESSION['khuid'].'&tmp='.time().'" type="'.$type_txt.'" ><a href="'.$proto.$server_out.':'.$port.$feed.'.m3u">'.$lng['click2listen'].'</a>';
    $i++;
    }
    $buffer.='</audio><br /><br />';
    echo $buffer;
	}
	if ($_SESSION['type']=='multi') include('listening-multiusers.php');
	
    }else{
	echo $lng['nolive'].' :<br /><br />
	<u>'.$lng['not_available'].'</u><br /><br />';
    }
?>
</div>
<?PHP
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$_SESSION['cong']) $cong_answer=$data[11];
	}
if (strstr($_SESSION['meeting_status'],"live") OR $server_beta=='true' ){ //for testing we trick it to believe it's live
?>
<br /><div id="number_at">
Please let us know how many people are listening on your side (yourself included) : <br />
<select name="attendance" onchange="javascript:update_at_no(this.value)">
<option value="">...</option>
<option value="1" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==1) echo 'selected="selected"';} ?>>1</option>
<option value="2" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==2) echo 'selected="selected"';} ?>>2</option>
<option value="3" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==3) echo 'selected="selected"';} ?>>3</option>
<option value="4" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==4) echo 'selected="selected"';} ?>>4</option>
<option value="5" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==5) echo 'selected="selected"';} ?>>5</option>
<option value="6" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==6) echo 'selected="selected"';} ?>>6</option>
<option value="7" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==7) echo 'selected="selected"';} ?>>7</option>
<option value="8" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==8) echo 'selected="selected"';} ?>>8</option>
<option value="9" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==9) echo 'selected="selected"';} ?>>9</option>
<option value="10" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==10) echo 'selected="selected"';} ?>>10</option>
<option value="11" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==11) echo 'selected="selected"';} ?>>11</option>
<option value="12" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==12) echo 'selected="selected"';} ?>>12</option>
<option value="13" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==13) echo 'selected="selected"';} ?>>13</option>
<option value="14" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==14) echo 'selected="selected"';} ?>>14</option>
<option value="15" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==15) echo 'selected="selected"';} ?>>15</option>
<option value="16" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==16) echo 'selected="selected"';} ?>>16</option>
<option value="17" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==17) echo 'selected="selected"';} ?>>17</option>
<option value="18" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==18) echo 'selected="selected"';} ?>>18</option>
<option value="19" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==19) echo 'selected="selected"';} ?>>19</option>
<option value="20" <?PHP if (isset($_SESSION['number_at'])){if ($_SESSION['number_at']==20) echo 'selected="selected"';} ?>>20</option>
</select>
</div>
<?PHP 
if ($cong_answer=="yes" ){
?>
<div id="sms_small" onclick="javascript:showdiv('sms','sms_small')">
Click here if you want to answer
</div>
<div id="sms">
Answer to (paragraph no / gem) :<br /><input id="p_no" name="p_no" type="text"></input><br /><br />
Your answer :<br /><textarea name="answer" id="answer"></textarea><br /><br />
<input id="send" type="button" name="send" value="Click here to send your Answer" onclick="javascript:send_answer()" /><br />
</div>
<?PHP
	}
}
?>

<?PHP
}
if (strstr($test, ".php")){
echo '</body></html>';
}else{
echo '</div>';
}
?>