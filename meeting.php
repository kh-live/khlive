<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

echo '<div id="page">
<h2>'.$lng['meeting'].'</h2>';
?>
<script type="text/javascript">
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
</script>
<iframe id="meeting" src="meeting-ajax.php" scrolling="no" onload="resizeIframe(this)" >
</iframe>

<?PHP
include 'meeting-songs.php';
include 'meeting-vmix.php';
?>
</div>
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