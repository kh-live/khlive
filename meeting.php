<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
echo '<div id="page">
<h2>'.$lng['meeting'].'</h2>
	<iframe id="meeting" src="meeting-ajax.php" >';
?>

</iframe>
<script type="text/javascript">
function showdiv(d1, d2){
if(d1.length < 1) { return; }
if(d2.length < 1) { return; }
        document.getElementById(d1).style.display = "block";
        document.getElementById(d2).style.display = "none";
}
function update_song(id, no){
if(id.length < 1) { return; }
if(no.length < 1) { return; }

var song=document.getElementById("song" +id+ "_audio");
song.src="kh-songs/iasn_E_" +no+".m4a";
song.type="audio/mp4";

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
    if (resp!=""){
	//there was an error
	var error = alert(resp);
    }
    }
   
    }
xmlhttp.open("GET","song.php?song_"+ id +"=" + no, true);
xmlhttp.send();
}
function random_song(){
var player = document.getElementById('rand_player');
if (player.paused){
var no=Math.floor((Math.random()*137)+1);
if (no<=9){
no="00"+no;
}
else if (no<=99){
no="0"+no;
}

player.src = "kh-songs/iasn_E_" +no+".m4a";
player.type="audio/mp4";
player.addEventListener('ended', random_song);
player.autoplay= true ;
player.load();
} else {
player.stop();
}
}
</script>
</div>
<div id="songs">
<a href="javascript:showdiv('song-small','songs')">>></a>
SONGS :<br /><br />
Song : <select id="song1" onchange="javascript:update_song(1,this.value)">
<?PHP
$i=1;
$tmp="";
echo '<option value="" >...</option>';
while ($i<=138){
$j=$i;
if ($i<=9) $j="00".$j;
if ($i<=99 AND $i>=10) $j="0".$j;
if (isset($_SESSION['song_1'])){
	if ($_SESSION['song_1']==$i) $tmp='selected="selected"';
}
echo '<option value="'.$j.'" '.$tmp.' >'.$j.'</option>';
$i++;
$tmp="";
}
?>
</select>
<audio id="song1_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_1'])) echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_1'].'.m4a" type="audio/mp4" >';
?>
 </audio><br /><br />
Song 2: <select id="song2" onchange="javascript:update_song(2,this.value)">
<?PHP
$i=1;
$tmp="";
echo '<option value="" >...</option>';
while ($i<=138){
$j=$i;
if ($i<=9) $j="00".$j;
if ($i<=99 AND $i>=10) $j="0".$j;
if (isset($_SESSION['song_2'])){
	if ($_SESSION['song_2']==$i) $tmp='selected="selected"';
}
echo '<option value="'.$j.'" '.$tmp.' >'.$j.'</option>';
$i++;
$tmp="";
}
?>
</select>
<audio id="song2_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_2'])) echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_2'].'.m4a" type="audio/mp4" >';
?>
 </audio><br /><br />
 Song 3: <select id="song3" onchange="javascript:update_song(3,this.value)">
<?PHP
$i=1;
$tmp="";
echo '<option value="" >...</option>';
while ($i<=138){
$j=$i;
if ($i<=9) $j="00".$j;
if ($i<=99 AND $i>=10) $j="0".$j;
if (isset($_SESSION['song_3'])){
	if ($_SESSION['song_3']==$i) $tmp='selected="selected"';
}
echo '<option value="'.$j.'" '.$tmp.' >'.$j.'</option>';
$i++;
$tmp="";
}
?>
</select>
<audio id="song3_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_3'])) echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_3'].'.m4a" type="audio/mp4" >';
?>
 </audio><br /><br />
Random songs:
<audio id="rand_player" controls preload="auto" onclick="javascript:random_song()" >
 </audio>
</div>
<div id="song-small">
<a href="javascript:showdiv('songs','song-small')"><<</a><br /><br />
S<br />
O<br />
N<br />
G<br />
S<br />
</div>
