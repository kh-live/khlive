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
echo '<html><head>
<link rel="stylesheet" type="text/css" href="css/'.$version.'--style.css" media="all" />
<link rel="stylesheet" type="text/css" href="css/'.$version.'--mobile.css" media="only screen and (max-width:840px)" />
<link type="text/css" rel="stylesheet" href="css/'.$version.'--default.css" media="only screen and (min-width:841px)" />
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
echo '<iframe id="listen_frame" src="http://'.$url.'/kh-live/listening.php?user='.$_SESSION['user'].'&cong='.$_SESSION['cong'].'"></iframe>';
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
xmlhttp.open("GET","listener_joined.php?action=update_at&user=<?PHP echo $_SESSION['user'];?>&number=" + no, true);
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
<?PHP echo '<h2>'.$lng['listening'].'</h2>';

echo $lng['listening_text'].'<br /><br />';

//detect navigator
$type_accept="";
if(strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")){
	//only mp3
	$type_accept="mp3";
}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Firefox")){
	//only ogg mp3 since v. 21
	$type_accept="all";
}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Mobile")){
	//everything
	$type_accept="mob";
}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Opera")){
	//only ogg
	$type_accept="ogg";
}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Chrome")){
	 //accept everything, attention string contains safari must be before Safari
	 $type_accept="all";
}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Safari")){
	//only mp3
	$type_accept="mp3";
}else{
	//everything
	$type_accept="all";
}
?>
<div id="feeds">
<?PHP	
	$db=file("db/streams");
	$live=file("db/live_streams");
	$no_streams=0;
	$no_streams_live=0;
	$buffer="";
    foreach($db as $line){
    $data=explode ("**",$line);
    	$is_live=0;
	if ($data[1]==$_SESSION['cong']){
	$no_streams+=1;
	$feed=$data[0];
	$type=$data[2];

    foreach($live as $live_line){
    $live_data=explode ("**",$live_line);
	if ($live_data[0]==$feed){
	$is_live=1;
	}
	}
	if ($is_live==1){
	
    $no_streams_live+=1;
    $type_txt="";
    if ($type=="mp3") $type_txt="audio/mpeg";
    if ($type=="ogg") $type_txt="audio/ogg";
    if ($type==$type_accept OR $type_accept=="all" OR $type_accept=="mob"){
    $buffer.='<audio controls autoplay> <source src="http://'.$server_out.':'.$port.$feed.'?user='.$_SESSION['user'].'&pass='.$_SESSION['cong'].'" type="'.$type_txt.'" ><a href="http://'.$server_out.':'.$port.$feed.'.m3u">'.$lng['click2listen'].'</a></audio><br /><br />';
	}else{
    $buffer.=$lng['alern_link'].' <a href="http://'.$server_out.':'.$port.$feed.'.m3u?user='.$_SESSION['user'].'&pass='.$_SESSION['cong'].'">'.$lng['click2listen'].'</a><br /><br />';
    }
    if ($type_accept=="mob"){
    $buffer.='<a href="http://'.$server_out.':'.$port.$feed.'.m3u">'.$lng['click2listen'].'</a><br /><br />';
    }
    }else{
	$buffer.='<u>'.$lng['not_available'].'</u><br /><br />';
    }
    }
    }
    if ($no_streams==0){
    echo $lng['nofeed_setup'].'<br /><br />';
    }
    if ($no_streams_live==0){
	echo $lng['nolive'].' :<br /><br />';
    }else{
	echo $lng['yeslive'].' :<br /><br />';
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
if ($no_streams_live>>0 OR $server_beta=='true' ){ //for testing we trick it to believe it's live
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