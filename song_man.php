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
function downloadToServer(i, t, max){
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
      
      if (max>=i+1){
      downloadToServer(i+1, t, max);
      }else{
      window.location="./song_man?type=song_no";
      }
}
  }
  tstmp = new Date();
xmlhttp1.open("GET","song_down.php?song_no="+i+"&type="+t+"&tmp=" +  tstmp.getTime() , true);
xmlhttp1.send();
cTimer=setInterval(function(){ getDstatus(i, ""+t+""); }, 2000);
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
   
    document.getElementById("progress_download").innerHTML="<b>Song no "+i +"</b><br />"+resp + " kB downloaded <br /><a style=\"color:red;font-size:12px;\" href=\"./song_man?type=song_no\">Cancel download (the current file will finish downloading in the background!)</a>";
    
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
<br /><br />Please note that you should use the orchestral version for your meetings. If piano version is on the server, it will automatically be used as a fallback in case the orchestral one is not present. <b>BOLD</b> heading indicates which type is selected in configuration.<br /><br />
<?PHP
if (isset($_GET['type'])){
	if ($_GET['type']=='filename'){
?>
<b>Manage by filename</b><br />
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
<b>Manage by song no</b><br /> You can change the video quality on configuration page.<br />

<table>
<?PHP
if (!isset($_SESSION['cong_lang'])){
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($data[0]==$_SESSION['cong']) {
	$_SESSION['cong_lang']=@$data[15];
		}
}
}
echo '<tr><td>Song no</td><td>piano version (outdated)</td><td>';
	if ($song_type=='normal'){
	echo '<b>orchestral (until 31.12.2016)</b></td><td>';
	}else{
	echo 'orchestral (until 31.12.2016)</td><td>';
	}
	if ($song_type=='joy'){
	echo '<b>joyfully (from 01.12.2016)</b><br /><input type="button" value="download all joyfully mp3" onclick="javascript:downloadToServer(1, \'joy\',151)"/></td><td>';
	}else{
	echo 'joyfully (from 01.12.2016)<br /><input type="button" value="download all joyfully mp3" onclick="javascript:downloadToServer(1, \'joy\',151)"/></td><td>';
	}
	if ($song_type=='vid'){
	echo '<b>music video '.$song_quality.'P (from 01.12.2016)</b><br /><input type="button" value="download all vid '. $song_quality.'" onclick="javascript:downloadToServer(1,\'vid'.$song_quality.'\',151)"/></td>';
	}else{
	echo 'music video '.$song_quality.'P (from 01.12.2016)<br /><input type="button" value="download all vid '. $song_quality.'" onclick="javascript:downloadToServer(1,\'vid'.$song_quality.'\',151)"/></td>';
	}
	echo '</tr>';
       for ($i=1 ; $i<=$max_song_no; $i++) {
       $song_no=$i;
       if ($i<=135){
        if ($i<=99) $song_no='0'.$i;
       if ($i<=9) $song_no='00'.$i;
       $info0='';
       $info1='<a href="javascript:downloadToServer('.$i.', \'piano\',0)">download to server</a>';
       $info2='<a href="javascript:downloadToServer('.$i.', \'orchestral\',0)">download to server</a>';
       $info3='<a href="javascript:downloadToServer('.$i.', \'joy\',0)">download to server</a>';
       if ($_SESSION['cong_lang']!='E'){
       $info4='Not yet available...';
       }else{
       $info4='<a href="javascript:downloadToServer('.$i.', \'vid'.$song_quality.'\',0)">download to server</a>';
      }
      if (file_exists('./kh-songs/iasn_E_'.$song_no.'.m4a')) $info0='<b style="color:green;" > m4a </b>';
	if (file_exists('./kh-songs/iasn_E_'.$song_no.'.mp3')) $info0.='<b style="color:green;" > mp3 </b>';
	if ($info0!='') $info1=$info0;
       if (file_exists('./kh-songs/iasnm_E_'.$song_no.'.mp3')) $info2='<b style="color:green;" >mp3</b>';
       if (file_exists('./kh-songs/sjjm_E_'.$song_no.'.mp3')) $info3='<b style="color:green;" >mp3</b>';
       if (file_exists('./kh-songs/sjjm_'.$_SESSION['cong_lang'].'_'.$song_no.'_r'.$song_quality.'P.mp4')) $info4='<b style="color:green;" >'.$_SESSION['cong_lang'].' mp4 ('.$song_quality.'P)</b>';
                     echo'<tr><td>'.$song_no.'</td><td>'.$info1.'</td><td>'.$info2.'</td><td>'.$info3.'</td><td>'.$info4.'</td></tr>';
	
	}elseif ($i<=151){
	$info2='<a href="javascript:downloadToServer('.$i.', \'new\',0)">download to server</a>';
	$info3='<a href="javascript:downloadToServer('.$i.', \'joy\',0)">download to server</a>';
	       if ($_SESSION['cong_lang']!='E'){
       $info4='Not yet available...';
       }else{
       $info4='<a href="javascript:downloadToServer('.$i.', \'vid'.$song_quality.'\',0)">download to server</a>';
      }
	if (file_exists('./kh-songs/sjjm_'.$_SESSION['cong_lang'].'_'.$song_no.'_r'.$song_quality.'P.mp4')) $info4='<b style="color:green;" >'.$_SESSION['cong_lang'].' mp4 ('.$song_quality.'P)</b>';
	if (file_exists('./kh-songs/snnw_E_'.$song_no.'.mp3')) $info2='<b style="color:green;" >mp3</b>';
	 if (file_exists('./kh-songs/sjjm_E_'.$song_no.'.mp3')) $info3='<b style="color:green;" >mp3</b>';
	echo'<tr><td>'.$song_no.'</td><td>n/a</td><td>'.$info2.'</td><td>'.$info3.'</td><td>'.$info4.'</td></tr>';
	}else{
	$info2='<a href="javascript:downloadToServer('.$i.', \'new\',0)">download to server</a>';
	if (file_exists('./kh-songs/snnw_E_'.$song_no.'.mp3')) $info2='<b style="color:green;" >mp3</b>';
	echo'<tr><td>'.$song_no.'</td><td>n/a</td><td>'.$info2.'</td><td>n/a</td><td>n/a</td></tr>';
	}
	}
?>
</table>
<?PHP
}
}
?>
</div>
