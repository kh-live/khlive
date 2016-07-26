<?PHP
//this page is either included or contacted directly in an iframe (for remote connection)
$print='ok';


$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
$a = session_id();
if ($a == ''){
session_start();
}
include "db/config.php";
include "lang.php";
//in case of i frame we need to set some session variables that we get through a get arg
$_SESSION['user']=$_GET['user'];
$_SESSION['cong']=$_GET['cong'];
$_SESSION['meeting_status']=implode("",file($temp_dir.'meeting_'.$_SESSION['cong']));
$_SESSION['test_meeting_status']=implode("",file($temp_dir.'test_meeting_'.$_SESSION['cong']));
echo '<html><head>
<style type="text/css">
body {
    color: black;
    font-family: sans-serif;
    font-size: 16px;
    margin: 0;
    overflow-x: hidden;
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
#number_at {
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
    width: 400px;
}
</style>
</head>
<body>';
}else{
//if it's from the master server, we need to redirect to the slave server
if ($server_beta=="master"){
$print='ko';
$url="";
$db=file("db/servers");
    foreach($db as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$url=$data[1];
	}
	}
if ($url==""){
echo 'Could not find your congregations server...';
}else{
//we check if the meeting is live or not
if ($_SESSION['meeting_status']=='live'){
echo '<div id="page"><iframe id="listen_frame" src="http://'.$url.'/kh-live/listening.php?user='.$_SESSION['user'].'&cong='.$_SESSION['cong'].'"></iframe></div>';
}else{
echo '<div id="page"><h2>'.$lng['listening'].'</h2><br /><br /><div id="feeds">'.$lng['nolive'].' :<br /><br /><u>'.$lng['not_available'].'</u><br /><br /></div>'.$lng['listen_records'].'<br /></div>';
}
}
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
xmlhttp.open("GET","listener_joined.php?action=update_at&user=<?PHP echo $_SESSION['user'];?>&cong=<?PHP echo $_SESSION['cong'];?>&number=" + no, true);
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
xmlhttp.open("GET","answer.php?action=sms_cancel&agent=client&client=<?PHP echo $_SESSION['user'];?>&cong=<?PHP echo $_SESSION['cong'];?>", true);
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
xmlhttp.send("action=sms&client=<?PHP echo $_SESSION['user'];?>&cong=<?PHP echo $_SESSION['cong'];?>&paragraph=" + paragraph + "&answer=" + answer);
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
xmlhttp.open("GET","sms_check.php?usr=<?PHP echo $_SESSION['user'];?>&cong=<?PHP echo $_SESSION['cong'];?>", true);
xmlhttp.send();
}
function counter (i){
 document.getElementById("sms_small").innerHTML=document.getElementById("sms_small").innerHTML + "."
 if (i<=9){
  count=setTimeout(function(){counter(i+1)}, 3000);
 }
}
</script>
<div id="page">
<?PHP echo '<h2>'.$lng['listening'].'</h2>'.$lng['listening_text'].'<br /><br />';
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

	if(strstr($_SESSION['meeting_status'],"live")){
	echo $lng['yeslive'].' :<br /><br />';
    
    if (strstr($_SERVER['HTTP_HOST'],'192.168.')){
    $server_out=$_SERVER['HTTP_HOST'];
    }elseif (file_exists($temp_dir.'global_ip')){
    $server_out=file_get_contents($temp_dir.'global_ip');
    }

    $buffer.='<audio controls autoplay>';
    $i=0;
    foreach ($feeds as $feed){
    $type=$types[$i];
	$type_txt="";
    if ($type=="mp3") $type_txt="audio/mpeg";
    if ($type=="ogg") $type_txt="audio/ogg";
    $buffer.='<source src="http://'.$server_out.':'.$port.$feed.'?user='.$_SESSION['user'].'&pass='.$_SESSION['cong'].'&tmp='.time().'" type="'.$type_txt.'" ><a href="http://'.$server_out.':'.$port.$feed.'.m3u">'.$lng['click2listen'].'</a>';
    $i++;
    }
    $buffer.='</audio><br /><br />';
    
    }else{
	echo $lng['nolive'].' :<br /><br />';
	$buffer.='<u>'.$lng['not_available'].'</u><br /><br />';
    }
	echo $buffer;
?>
</div>
<?PHP
echo $lng['listen_records'].'.<br />';

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
</select>
</div>
<?PHP 
if ($cong_answer=="yes" ){
?>
<div id="sms_small" onclick="javascript:showdiv('sms','sms_small')">
Click here if you want to answer
</div>
<div id="sms">
Answer to (paragraph no / highlight) :<br /><input id="p_no" name="p_no" type="text"></input><br /><br />
Your answer :<br /><textarea name="answer" id="answer"></textarea><br /><br />
<input id="send" type="button" name="send" value="Click here to send your Answer" onclick="javascript:send_answer()" /><br />
</div>
<?PHP
	}
}
?>
</div>
<?PHP
}
if (strstr($test, ".php")){
echo '</body></html>';
}
?>