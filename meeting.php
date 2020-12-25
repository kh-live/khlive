<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$db4=file("db/cong");
    foreach($db4 as $line){
    $data=explode ("**",$line);
	if ($data[0]==$_SESSION['cong']){
	$meeting_type=$data[5];
	$jitsi_cong_address=@$data[16];
}
}
echo '<div id="page">
<h2>'.$lng['meeting'].'</h2>';
if ($meeting_type=='jitsi'){
include 'meeting-jitsi.php';
}else{
if ($server_beta=='master' AND $_SESSION['type']!="user"){
$ip='';
$url='';
$db1=file("db/servers");
    foreach($db1 as $line){
        $data=explode ("**",$line);
	if (strstr($data[3],$_SESSION['cong'])){
	$ip=$data[1];
	$url=$data[0];
	}
	}
if ($ip!='' AND $url!=''){
echo '<b>You cannot manage the meeting from the main server!</b><br />Here is the link to your local server (at the kingdom hall) where you\'ll be able to manage the meeting : <br />
<a href="http://'.$url.'">'.$url.'</a> ( <a href="http://'.$ip.'">'.$ip.'</a> )';
}
}else{
if (@$timing_conf=='yes'){
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
<?PHP
if ($meeting_type!="none"){
?>
<div id="timing_container">
<?PHP
include 'meeting-time.php';
?>
</div>
<?PHP
}
} ?>
<script type="text/javascript">
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
</script>
<iframe id="meeting" src="meeting-ajax.php" scrolling="no" onload="resizeIframe(this)" >
</iframe>

<?PHP
if ($meeting_type!="none"){
include 'meeting-songs.php';
include 'meeting-vmix.php';
}
?>
<script type="text/javascript">
var showWarning='no';
window.onbeforeunload = testT;
function confirmExit()
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
    if (resp.search('down')!= -1)
    {
    //meeting not live. ok
    showWarning="no";
    }else{
	showWarning="yes";
    
    }
    }
  }
   tstmp = new Date();    
   
xmlhttp.open("GET","api.php?check=<?PHP echo $_SESSION['cong'] ; ?>&tmp=" +  tstmp.getTime() ,false);
xmlhttp.send();
	}
function testT()
{
confirmExit();
 if (showWarning=="yes"){
return "Please don't forget to STOP the meeting before closing the page!";
}
}
</script>
<?PHP
}
}
?>