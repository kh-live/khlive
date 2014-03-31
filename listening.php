<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<script type="text/javascript">
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
	document.getElementById("sms_small").onclick="";
	document.getElementById("sms_small").style.cursor= "wait";
	document.getElementById("sms_small").onmouseover = function() {
    this.style.textDecoration = "none";
    }
	 document.getElementById("sms_small").innerHTML="Your answer is : " + resp + "<br />";
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
 var count=setTimeout(function(){counter(i+1)}, 3000);
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
	//only ogg
	$type_accept="ogg";
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
	/*$response="";
	$fp = fsockopen( $server, $port , $errno, $errstr, 1); //last param is timeout 1sec
	if (!$fp) {
	 echo $lng['nolive'].'<br /><br />';
	} else {
	$out = "GET ".$feed." HTTP/1.1\r\n";
    $out .= "Host: ".$server.":".$port."\r\n";
    $out .="Authorization: Basic ".base64_encode($_SESSION['user'].":".$_SESSION['cong'])."\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
        $response=fgets($fp, 128);
	
    if (strstr($response,"200 OK")){*/
 
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
if ($no_streams_live>>0 AND $cong_answer=="yes" ){ //OR 1==1
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
?>
</div>
