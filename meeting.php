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
</div>
<div id="songs">
<a href="javascript:showdiv('song-small','songs')">>> HIDE</a><br /><br />
Song 1 : <select id="song1" onchange="javascript:update_song(1,this.value)">
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
<?PHP
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$_SESSION['cong']) {
		$meeting_type=$data[5];
		}
	}
if ($meeting_type=="direct"){
if (isset($_SESSION['song_1'])){
echo '<br /><input type="submit" id="play_1" value="Play Song '.$_SESSION['song_1'].'" /><input type="submit" id="stop_1" value="Stop..." disabled="disabled" />';
}else{
echo '<br /><input type="submit" id="play_1" value="select a song..." disabled="disabled" /><input type="submit" id="stop_1" value="Stop..." disabled="disabled" />';
}
}else{
?>
<audio id="song1_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_1'])) echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_1'].'.m4a" type="audio/mp4" >';
?>
 </audio>
 <?PHP
 }
 ?>
 <br /><br />
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
<?PHP
if ($meeting_type=="direct"){
if (isset($_SESSION['song_2'])){
echo '<br /><input type="submit" id="play_2" value="Play Song '.$_SESSION['song_2'].'" /><input type="submit" id="stop_2" value="Stop..." disabled="disabled" />';
}else{
echo '<br /><input type="submit" id="play_2" value="select a song..." disabled="disabled" /><input type="submit" id="stop_2" value="Stop..." disabled="disabled" />';
}
}else{
?>
<audio id="song2_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_2'])) echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_2'].'.m4a" type="audio/mp4" >';
?>
 </audio>
 <?PHP
 }
 ?><br /><br />
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
<?PHP
if ($meeting_type=="direct"){
if (isset($_SESSION['song_3'])){
echo '<br /><input type="submit" id="play_3" value="Play Song '.$_SESSION['song_3'].'" /><input type="submit" id="stop_3" value="Stop..." disabled="disabled" />';
}else{
echo '<br /><input type="submit" id="play_3" value="select a song..." disabled="disabled" /><input type="submit" id="stop_3" value="Stop..." disabled="disabled" />';
}
}else{
?>
<audio id="song3_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_3'])) echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_3'].'.m4a" type="audio/mp4" >';
?>
 </audio><br /><br />
Random songs:
<audio id="rand_player" controls preload="auto" >
<source src="kh-songs/iasn_E_<?PHP echo rand(100,138); ?>.m4a" type="audio/mp4" >
 </audio>
 <?PHP
 }
 ?>
 <br /> <br />136 : The Kingdom is in place - Let it come!<br />
 137 : Grant us boldness<br />
 138 : Jehovah is your Name
</div>
<div id="song-small" onclick="javascript:showdiv('songs','song-small')">
<br />S<br />
O<br />
N<br />
G<br />
S<br />
</div>
<script type="text/javascript">
function showdiv(d1, d2){
if(d1.length < 1) { return; }
if(d2.length < 1) { return; }
        document.getElementById(d1).style.marginRight = "0px";
        document.getElementById(d2).style.marginRight = "-320px";
}
function update_song(id, no){
if(id.length < 1) { return; }
if(no.length < 1) { return; }
<?PHP
if ($meeting_type=="direct"){
?>
var song=document.getElementById("play_" +id);
song.value="Play Song "+no;
song.disabled = false;
getElementById("stop_" +id).value= "Stop Song "+no;
<?PHP
}else{
?>
var song=document.getElementById("song" +id+ "_audio");
song.src="kh-songs/iasn_E_" +no+".m4a";
song.type="audio/mp4";
<?PHP
}
?>
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
<?PHP
if ($meeting_type!="direct"){
?>
document.getElementById('rand_player').addEventListener('ended',function(e){
var player = document.getElementById('rand_player');

var no=Math.floor((Math.random()*137)+1);
if (no<=9){
no="00"+no;
}
else if (no<=99){
no="0"+no;
}

player.src = "kh-songs/iasn_E_" +no+".m4a";
player.type="audio/mp4";
player.autoplay= true ;
player.load();
});
<?PHP
}else{
?>
function play_song(id, song){
if(id.length < 1) { return; }
if(no.length < 1) { return; }

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
	document.getElementById("play_"+id).value="Play Pause" + song;
	document.getElementById("stop_"+id).disabled = false;
	document.getElementById("play_"+id).onclick= function(){
	var no=this.id.substr(5);
	var songNo=this.value.substr(10);
		pause_song(no, songNo);
		}
    }
    }
   
    }
xmlhttp.open("GET","song.php?play=" + song, true);
xmlhttp.send();
}
function stop_song(id, song){
if(id.length < 1) { return; }
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
	document.getElementById("play_"+id).value = "Play Song " + song;
	document.getElementById("stop_"+id).disabled = true;
	document.getElementById("play_"+id).onclick= function(){
	var no=this.id.substr(5);
	var songNo=this.value.substr(10);
		play_song(no, songNo);
		}
    }
    }
   
    }
xmlhttp.open("GET","song.php?stop=true", true);
xmlhttp.send();
}
function pause_song(id, song){
if(id.length < 1) { return; }
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
    }
    }
   
    }
xmlhttp.open("GET","song.php?pause=true", true);
xmlhttp.send();
}
for (var i = 1; i <= 3; ++i) {
var link = document.getElementById("play_"+i);
	link.onclick= function(){
	var no=this.id.substr(5);
	var songNo=this.value.substr(10);
		play_song(no, songNo);
		}
}
for (var i = 1; i <= 3; ++i) {
var link = document.getElementById("stop_"+i);
	link.onclick= function(){
	var no=this.id.substr(5);
	var songNo=this.value.substr(10);
		stop_song(no, songNo);
		}
}
<?PHP
}
?>
</script>