<?PHP
if (isset($vmix)){
	if ($vmix=='yes'){
?>
<div id="vmix_title" onclick="javascript:vMixToggle()">&uArr; vMix &uArr;</div><div id="vmix_ctrl">Connecting to vmix...
</div>
<script type="text/javascript">
var parseXml;

if (typeof window.DOMParser != "undefined") {
    parseXml = function(xmlStr) {
        return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
    };
} else if (typeof window.ActiveXObject != "undefined" &&
       new window.ActiveXObject("Microsoft.XMLDOM")) {
    parseXml = function(xmlStr) {
        var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = "false";
        xmlDoc.loadXML(xmlStr);
        return xmlDoc;
    };
} else {
    throw new Error("No XML parser found");
}

window.onload=vMixGetStart();
function vMixToggle(){
if ( document.getElementById("vmix_ctrl").style.marginBottom==''){
document.getElementById("vmix_ctrl").style.marginBottom="0";
document.getElementById("vmix_title").innerHTML="&dArr; vMix &dArr;";
}else{
document.getElementById("vmix_ctrl").style.marginBottom="";
document.getElementById("vmix_title").innerHTML="&uArr; vMix &uArr;";
}
}
function vMixGetStart(){
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
    var xml = parseXml(resp);
    var yt = xml.getElementsByTagName("input")[0].textContent;
    if (yt!="<?PHP echo 'YT-'.date('Y',time()).'-'.$_SESSION['cong'];?>.jpg"){
   document.getElementById("vmix_ctrl").innerHTML="connected! Loading year text ...";
   vMixLoadYT();
   }else{
   document.getElementById("vmix_ctrl").innerHTML="connected!";
   }
   document.getElementById("vmix_ctrl").innerHTML="<div id=\"vmix_lib\"><b>LIBRARY</b><br/>Select the files you want to display : <br /> Note it has to be in the following directory : <b><?PHP echo addslashes($vmix_path); ?></b><br /><br /></div><div id=\"vmix_status\">Loading...</div>";
document.getElementById("vmix_lib").innerHTML+="<div id=\"vMixFile1\"> <input id=\"file1\" type=\"file\" onchange=\"javascript:vMixPreLoad(1)\"></div>";
    
   window.setInterval(function(){
  vMixGetStatus();
}, 5000);
       }else if (xmlhttp.readyState==4){
     document.getElementById("vmix_ctrl").innerHTML="error connecting to vmix. is vmix running? is the address right?" +xmlhttp.responseText + "<br /><a href=\"javascript:vMixGetStart()\"> Click here to Try again</a>";
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api", true);
xmlhttp.send();
}
function vMixGetStatus(){
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
    var xml = parseXml(resp);
    var inputs = xml.getElementsByTagName("input");
    var active = xml.getElementsByTagName("active")[0].textContent;
    document.getElementById("vmix_status").innerHTML="<b>CONTROLS</b>";
    for (i = 0; i < inputs.length; i++) { 
	var input=inputs[i];
	var j= i+1;
	var duration = input.getAttribute("duration");
	var position = input.getAttribute("position");

var totalSec = duration / 1000;
var hours = parseInt( totalSec / 3600 ) % 24;
var minutes = parseInt( totalSec / 60 ) % 60;
var seconds = totalSec % 60;

var totalSec2 = position / 1000;
var hours2 = parseInt( totalSec2 / 3600 ) % 24;
var minutes2 = parseInt( totalSec2 / 60 ) % 60;
var seconds2 = totalSec2 % 60;

 if (j==1){
 var timing="year text";
 }else{
var timing= (hours < 10 ? "0" + hours : hours) +":"+ (minutes < 10 ? "0" + minutes : minutes) +":"+ (seconds  < 10 ? "0" + Math.round(seconds) : Math.round(seconds)) +" ("+(hours2 < 10 ? "0" + hours2 : hours2) +":"+ (minutes2 < 10 ? "0" + minutes2 : minutes2) +":"+ (seconds2  < 10 ? "0" + Math.round(seconds2) : Math.round(seconds2))+")";
}
	if (j==active){
	 document.getElementById("vmix_status").innerHTML+="<br /><br /><b style=\"color:red;\">Input "+ j + " - "+ timing + " : </b>";
	}else{
	 document.getElementById("vmix_status").innerHTML+="<br /><br />Input "+ j + " - "+ timing + " : ";
	}
    document.getElementById("vmix_status").innerHTML+="<br /><input id=\"video"+ j +"\" type=\"text\" disabled=\"disabled\" value=\"" + input.textContent + "\"/>";
	if (input.getAttribute('state')=="Paused"){
	  if (j==1){
		if (active==1){
	 document.getElementById("vmix_status").innerHTML+="<input style=\"color:red;border-color:red;\" type=\"submit\" value=\"playing\" />";
		}else{
		document.getElementById("vmix_status").innerHTML+="<input type=\"submit\" onclick=\"javascript:vMixActivate("+ j +","+ active +")\" value=\"&#9658; play\" />";
	 }
	 }else{
	 document.getElementById("vmix_status").innerHTML+="<input type=\"submit\" onclick=\"javascript:vMixActivate("+ j +","+ active +")\" value=\"&#9658; play\" />";
	 document.getElementById("vmix_status").innerHTML+="<input type=\"submit\" onclick=\"javascript:vMixStop("+ j +")\" value=\"&#9209; stop\" />";
	 }
	}else if (input.getAttribute('state')=="Completed"){
	document.getElementById("vmix_status").innerHTML+="<input type=\"submit\" onclick=\"javascript:vMixStop("+ j +")\" value=\"&#9209; stop\" /><input type=\"submit\" onclick=\"javascript:vMixRePlay("+ j +","+ active +")\" value=\"&#9658; re-play\" />";
	}else{
	 document.getElementById("vmix_status").innerHTML+="<input style=\"color:red;border-color:red;\" type=\"submit\" value=\"playing\" /><input type=\"submit\" onclick=\"javascript:vMixPause("+ j +")\" value=\"&#9612;&#9612; pause\" /><input type=\"submit\" onclick=\"javascript:vMixStop("+ j +")\" value=\"&#9194; restart\" />";
	}
	if (j!=1){
	document.getElementById("vmix_status").innerHTML+="<input type=\"submit\" onclick=\"javascript:vMixClose("+ j +")\" value=\"&#9167; close\" /> ";
	}
    }
       }else if (xmlhttp.readyState==4){
     document.getElementById("vmix_status").innerHTML="error connecting to vmix. is vmix running? is the address right?" +xmlhttp.responseText + "<br /><a href=\"javascript:vMixGetS()\"> Click here to Try again</a>";
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api", true);
xmlhttp.send();
}
function vMixLoadYT(){
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
   }else if (xmlhttp.readyState==4) {
    document.getElementById("vmix_ctrl").innerHTML="<div id=\"vmix_status\">error loading year text!</div> Is file in right directory?" +xmlhttp.responseText + "<br /><a href=\"javascript:vMixGetStart()\"> Click here to Try again</a>";

    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=AddInput&value=Image|<?PHP echo urlencode($vmix_path.'YT-'.date('Y',time()).'-'.$_SESSION['cong']);?>.jpg", true);
xmlhttp.send();
}
function basename(path) {
   return path.split('\\').reverse()[0];
}
function vMixPreLoad(id){
    var setName=document.getElementById("file"+id).value.replace("C:\\fakepath\\","");
    document.getElementById("vMixFile"+id).innerHTML="<input id=\"file"+ id +"\" type=\"text\" value=\""+ setName +"\" disabled=\"disabled\" ><input type=\"submit\" onclick=\"javascript:vMixLoad("+ id +")\" value=\"&#10560; load\" /></div>";
    document.getElementById("vmix_lib").innerHTML+="<div id=\"vMixFile"+ (1+id) +"\"> <input id=\"file"+ (1+id) +"\" type=\"file\" onchange=\"javascript:vMixPreLoad("+ (1+id) +")\" ></div>";
}
function vMixLoad(id){
var vFile = basename(document.getElementById("file" + id).value);
if ((vFile.toLowerCase().indexOf(".jpg")== -1 ) && (vFile.toLowerCase().indexOf(".jpeg")== -1 ) && (vFile.toLowerCase().indexOf(".bmp")== -1 ) && (vFile.toLowerCase().indexOf(".png")== -1 )){
var type="Video";
}else{
var type="Image";
}
if (vFile !== null) {
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
  vMixGetStatus();
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error loading file. Is file in a directory accessible by vmix?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=AddInput&value=" + type + "|<?PHP echo urlencode($vmix_path); ?>" + vFile, true);
xmlhttp.send();
}
}
function vMixPlay(id){
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

  vMixGetStatus();
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error playing file. Is file loaded?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=Play&input=" + id, true);
xmlhttp.send();
}
function vMixRePlay(id, active){
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
    <?PHP
    if ($vmix_auto_pause=='yes') echo 'vMixPause(active);';
    ?>
    vMixActivate(id,active);
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error playing file. Is file loaded?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=Restart&input=" + id, true);
xmlhttp.send();
}
function vMixStop(id){
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
  vMixGetStatus();
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error playing file. Is file loaded?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=Restart&input=" + id, true);
xmlhttp.send();
}
function vMixPause(id){
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
  vMixGetStatus();
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error pausing file. Is file loaded?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=Pause&input=" + id, true);
xmlhttp.send();
}
function vMixActivate(id,active){
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
            <?PHP
    if ($vmix_auto_pause=='yes') echo 'vMixPause(active);';
    ?>
   vMixPlay(id, active);
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error activating file. Is file loaded?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=ActiveInput&input=" + id, true);
xmlhttp.send();
}
function vMixClose(id){
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
vMixGetStatus();
       }else if (xmlhttp.readyState==4) {
     document.getElementById("vmix_status").innerHTML="error removing file. Is file loaded?" +xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","http://<?PHP echo $vmix_url;?>/api?function=RemoveInput&input=" + id, true);
xmlhttp.send();
}
</script>
<?PHP
	}
}
?>
