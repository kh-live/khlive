<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$test=$_SERVER['REQUEST_URI'];
$url_parts = parse_url($test);
?>
<script type="text/javascript">
function show_confirm(i)
{

var r=confirm("Are you sure you want to delete this song :" + i +" ?");
if (r==true)
  {
  window.location="./delete.php?type=song&file=" + i ;
  }
else
  {
  window.location="<?PHP echo $url_parts['path'] ; ?>";
  }
 
}
function update_type(url){
  window.location="<?PHP echo $url_parts['path'] ; ?>?type=" + url;
}
function downloadToServer(i, t){
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp1=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp1=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp1.onreadystatechange=function()
  {
  if (xmlhttp1.readyState==4 && xmlhttp1.status==200)
    {
    resp=xmlhttp1.responseText;
      clearInterval(cTimer);
      window.location="./song_man?type=song_no";
}
  }
  tstmp = new Date();
xmlhttp1.open("GET","song_down.php?song_no="+i+"&type="+t+"&tmp=" +  tstmp.getTime() , true);
xmlhttp1.send();
var cTimer=setInterval(function(){ getDstatus(i, ""+t+""); }, 2000);
document.getElementById("please_wait").style.display="block";
}

function getDstatus(i, t){
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp2=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp2.onreadystatechange=function()
  {
  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    resp=xmlhttp2.responseText;
   
    document.getElementById("progress_download").innerHTML=resp + " kB downloaded";
    
}
  }
  tstmp = new Date();
xmlhttp2.open("GET","./song_down_stat.php/?song_no="+i+"&type="+t+"&tmp=" +  tstmp.getTime() , true);
xmlhttp2.send();
}
</script>
<div id="please_wait"><br /><br />Please wait for download to finish...<br /><h3 id="progress_download"></h3></div>
<div id="page">
<h2>Manage Songs</h2>
Select how to manage songs <br />
<select id="type" onchange="javascript:update_type(this.value)">
	<option value="">...</option>
	<option value="filename">file name</option>
	<option value="song_no">song number</option>
</select>
<br /><br />Please note that you should use the orchestral version for your meetings. If both versions are on the server, it will automatically use the orchestral one.<br /><br />
<?PHP
if (isset($_GET['type'])){
	if ($_GET['type']=='filename'){
?>
Manage by filename
<table>
<?PHP
echo '<tr><td><b>'.$lng['file'].'</b></td><td><b>'.$lng['size'].'</b></td><td><b>'.$lng['actions'].'</b></td></tr>';
$songs_db=array();
 if ($dh = @opendir("./kh-songs")) {
       while (($file = readdir($dh)) !== false) {
           if (($file != '.') && ($file != '..')&& ($file != 'index.php')){ 

	   $info=filesize("./kh-songs/".$file);
	   if ($info>=1048576){
	   $info=round($info/1048576,1);
	   $info.=" MB";
	   }elseif($info>=1024){
	   $info=round($info/1024,1);
	   $info.=" kB";
	   }else{
	    $info.=" B";
	   }
                     $songs_db[]='<tr><td>'.$file.'</td><td>'.$info.'</td><td><a href="javascript:show_confirm(\'../kh-songs/'.$file.'\')">Remove</a></td></tr>';

                     }
		}
	closedir($dh);
	}
	asort($songs_db);
	foreach($songs_db as $song_l){
	echo $song_l;
	}
?>
</table>
<?PHP
}else{
?>
Manage by song no
<table>
<?PHP
echo '<tr><td><b>Song no</b></td><td><b>piano version (outdated)</b></td><td><b>orchestral</b></td></tr>';
       for ($i=1 ; $i<=$max_song_no; $i++) {
       $song_no=$i;
       if ($i<=135){
        if ($i<=99) $song_no='0'.$i;
       if ($i<=9) $song_no='00'.$i;
       $info0='';
       $info1='<a href="javascript:downloadToServer('.$i.', \'piano\')">download to server</a>';
       $info2='<a href="javascript:downloadToServer('.$i.', \'orchestral\')">download to server</a>';
      
      if (file_exists('./kh-songs/iasn_E_'.$song_no.'.m4a')) $info0='<b style="color:green;" > m4a </b>';
	if (file_exists('./kh-songs/iasn_E_'.$song_no.'.mp3')) $info0.='<b style="color:green;" > mp3 </b>';
	if ($info0!='') $info1=$info0;
       if (file_exists('./kh-songs/iasnm_E_'.$song_no.'.mp3')) $info2='<b style="color:green;" >mp3</b>';
                     echo'<tr><td>'.$song_no.'</td><td>'.$info1.'</td><td>'.$info2.'</td></tr>';
	}else{
	$info2='<a href="javascript:downloadToServer('.$i.', \'new\')">download to server</a>';
	if (file_exists('./kh-songs/snnw_E_'.$song_no.'.mp3')) $info2='<b style="color:green;" >mp3</b>';
	echo'<tr><td>'.$song_no.'</td><td>n/a</td><td>'.$info2.'</td></tr>';
	}
	}
?>
</table>
<?PHP
}
}
?>
</div>
