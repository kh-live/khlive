<?PHP
if (is_file($temp_dir.'song_1_'.$_SESSION['cong'])){
$_SESSION['song_1']=file_get_contents($temp_dir.'song_1_'.$_SESSION['cong']);
}
if (is_file($temp_dir.'song_2_'.$_SESSION['cong'])){
$_SESSION['song_2']=file_get_contents($temp_dir.'song_2_'.$_SESSION['cong']);
}
if (is_file($temp_dir.'song_3_'.$_SESSION['cong'])){
$_SESSION['song_3']=file_get_contents($temp_dir.'song_3_'.$_SESSION['cong']);
}
?>
<div id="songs">
<a href="javascript:showdiv('song-small','songs')">>> HIDE</a><br /><br />
Song 1 : <select id="song1" onchange="javascript:update_song(1,this.value)">
<?PHP
$i=1;
$tmp="";
echo '<option value="" >...</option>';
while ($i<=$max_song_no){
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
if ($song_dev=="server" OR $song_dev=="vmix"){
if (isset($_SESSION['song_1'])){
echo '<br /><input type="submit" id="play_1" value="Play Song '.$_SESSION['song_1'].'" /><input type="submit" id="stop_1" value="Stop Song '.$_SESSION['song_1'].'" disabled="disabled" />';
}else{
echo '<br /><input type="submit" id="play_1" value="select a song..." disabled="disabled" /><input type="submit" id="stop_1" value="Stop..." disabled="disabled" />';
}
}else{
	if ($song_type=='normal'){
?>
<audio id="song1_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_1'])){
if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_1'].".mp3")){
 echo '<source src="kh-songs/iasnm_E_'.$_SESSION['song_1'].'.mp3" type="audio/mpeg" >';

	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_1'].".mp3")){
	 echo '<source src="kh-songs/snnw_E_'.$_SESSION['song_1'].'.mp3" type="audio/mpeg" >';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_1'].".mp3")){
	 echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_1'].'.mp3" type="audio/mpeg" >';
	}else{
	 echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_1'].'.m4a" type="audio/mp4" >';
	}

 }
?>
 </audio>
 <?PHP
 }elseif ($song_type=='joy'){
 echo '<audio id="song1_audio" controls preload="none" >';
if (isset($_SESSION['song_1'])){
if (is_file($web_server_root."kh-live/kh-songs/sjjm_E_".$_SESSION['song_1'].".mp3")){
 echo '<source src="kh-songs/sjjmm_E_'.$_SESSION['song_1'].'.mp3" type="audio/mpeg" >';
	}
 }
echo '</audio>';
 }else{
 echo 'cant play video on client yet. change setting from "client" to "vmix" in config';
 }
 }
 ?>
 <br /><br />
Song 2: <select id="song2" onchange="javascript:update_song(2,this.value)">
<?PHP
$i=1;
$tmp="";
echo '<option value="" >...</option>';
while ($i<=$max_song_no){
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
if ($song_dev=="server" OR $song_dev=="vmix"){
if (isset($_SESSION['song_2'])){
echo '<br /><input type="submit" id="play_2" value="Play Song '.$_SESSION['song_2'].'" /><input type="submit" id="stop_2" value="Stop Song '.$_SESSION['song_2'].'" disabled="disabled" />';
}else{
echo '<br /><input type="submit" id="play_2" value="select a song..." disabled="disabled" /><input type="submit" id="stop_2" value="Stop..." disabled="disabled" />';
}
}else{
	if ($song_type=='normal'){
?>
<audio id="song2_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_2'])){
if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_2'].".mp3")){
 echo '<source src="kh-songs/iasnm_E_'.$_SESSION['song_2'].'.mp3" type="audio/mpeg" >';

	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_2'].".mp3")){
	 echo '<source src="kh-songs/snnw_E_'.$_SESSION['song_2'].'.mp3" type="audio/mpeg" >';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_2'].".mp3")){
	 echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_2'].'.mp3" type="audio/mpeg" >';
	}else{
	 echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_2'].'.m4a" type="audio/mp4" >';
	}

 }
?>
 </audio>
 <?PHP
  }elseif ($song_type=='joy'){
  echo '<audio id="song2_audio" controls preload="none" >';
if (isset($_SESSION['song_2'])){
if (is_file($web_server_root."kh-live/kh-songs/sjjm_E_".$_SESSION['song_2'].".mp3")){
 echo '<source src="kh-songs/sjjmm_E_'.$_SESSION['song_2'].'.mp3" type="audio/mpeg" >';
	}
 }
echo '</audio>';
 }else{
 echo 'cant play video on client yet. change setting from "client" to "vmix" in config';
 }
 }
 ?><br /><br />
 Song 3: <select id="song3" onchange="javascript:update_song(3,this.value)">
<?PHP
$i=1;
$tmp="";
echo '<option value="" >...</option>';
while ($i<=$max_song_no){
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
if ($song_dev=="server" OR $song_dev=="vmix"){
if (isset($_SESSION['song_3'])){
echo '<br /><input type="submit" id="play_3" value="Play Song '.$_SESSION['song_3'].'" /><input type="submit" id="stop_3" value="Stop Song '.$_SESSION['song_3'].'" disabled="disabled" />';
}else{
echo '<br /><input type="submit" id="play_3" value="select a song..." disabled="disabled" /><input type="submit" id="stop_3" value="Stop..." disabled="disabled" />';
}
if ($song_dev=='server'){
echo '<br /><br />Random Songs:<br /><input type="submit" id="play_rand" value="Play Random" /><input type="submit" id="stop_rand" value="Stop Random" disabled="disabled" />';
}
}else{
	if ($song_type=='normal'){
?>
<audio id="song3_audio" controls preload="none" >
<?PHP
if (isset($_SESSION['song_3'])){
if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$_SESSION['song_3'].".mp3")){
 echo '<source src="kh-songs/iasnm_E_'.$_SESSION['song_3'].'.mp3" type="audio/mpeg" >';

	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$_SESSION['song_3'].".mp3")){
	 echo '<source src="kh-songs/snnw_E_'.$_SESSION['song_3'].'.mp3" type="audio/mpeg" >';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$_SESSION['song_3'].".mp3")){
	 echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_3'].'.mp3" type="audio/mpeg" >';
	}else{
	 echo '<source src="kh-songs/iasn_E_'.$_SESSION['song_3'].'.m4a" type="audio/mp4" >';
	}

 }
?>
 </audio><br /><br />
Random songs:
<audio id="rand_player" controls preload="auto" >
<?PHP
$rand_song = rand(100,$max_song_no);
if (isset($rand_song)){
if (is_file($web_server_root."kh-live/kh-songs/iasnm_E_".$rand_song.".mp3")){
 echo '<source src="kh-songs/iasnm_E_'.$rand_song.'.mp3" type="audio/mpeg" >';

	}elseif (is_file($web_server_root."kh-live/kh-songs/snnw_E_".$rand_song.".mp3")){
	 echo '<source src="kh-songs/snnw_E_'.$rand_song.'.mp3" type="audio/mpeg" >';
	}elseif (is_file($web_server_root."kh-live/kh-songs/iasn_E_".$rand_song.".mp3")){
	 echo '<source src="kh-songs/iasn_E_'.$rand_song.'.mp3" type="audio/mpeg" >';
	exec("/usr/bin/mocp -l ".$web_server_root."kh-live/kh-songs/iasn_E_".$rand_song.".mp3");
	}else{
	 echo '<source src="kh-songs/iasn_E_'.$rand_song.'.m4a" type="audio/mp4" >';
	}

 }
 ?>
 </audio>
 <?PHP
  }elseif ($song_type=='joy'){
  echo '<audio id="song3_audio" controls preload="none" >';
if (isset($_SESSION['song_3'])){
if (is_file($web_server_root."kh-live/kh-songs/sjjm_E_".$_SESSION['song_3'].".mp3")){
 echo '<source src="kh-songs/sjjmm_E_'.$_SESSION['song_3'].'.mp3" type="audio/mpeg" >';
	}
 }
echo '</audio>';
 }else{
 echo 'cant play video on client yet. change setting from "client" to "vmix" in config';
 }
 }
 ?>
</div>
<div id="song-small" onclick="javascript:showdiv('songs','song-small')">
<br />S<br />
O<br />
N<br />
G<br />
S<br /><br />
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
if ($song_dev=="server" OR $song_dev=="vmix"){
?>
var song=document.getElementById("play_" +id);
song.value="Play Song "+no;
song.disabled = false;
document.getElementById("stop_" +id).value= "Stop Song "+no;
<?PHP
}else{
?>
var song=document.getElementById("song" +id+ "_audio");
if (resp=="iasnm-mp3"){
song.src="kh-songs/iasnm_E_" +no+".mp3";
song.type="audio/mpeg";
}
if (resp=="snnw-mp3"){
song.src="kh-songs/snnw_E_" +no+".mp3";
song.type="audio/mpeg";
}
if (resp=="iasn-mp3"){
song.src="kh-songs/iasn_E_" +no+".mp3";
song.type="audio/mpeg";
}
if (resp=="iasn-m4a"){
song.src="kh-songs/iasn_E_" +no+".m4a";
song.type="audio/mp4";
}
if (resp=="sjjm-mp3"){
song.src="kh-songs/sjjm_E_" +no+".mp3";
song.type="audio/mpeg";
}
<?PHP
}
?>
    }
   
    }
xmlhttp.open("GET","song.php?song_"+ id +"=" + no, true);
xmlhttp.send();
}
<?PHP
if ($song_dev=="client"){
?>
document.getElementById('rand_player').addEventListener('ended',function(e){
var player = document.getElementById('rand_player');

var no=Math.floor((Math.random()*135)+1);
if (no<=9){
no="00"+no;
}
else if (no<=99){
no="0"+no;
}
<?PHP
if ($song_type=='normal'){
echo 'player.src = "kh-songs/iasnm_E_" +no+".mp3";
player.type="audio/mpeg";';
}elseif ($song_type=='joy'){
echo 'player.src = "kh-songs/sjjm_E_" +no+".mp3";
player.type="audio/mpeg";';
}
?>
player.autoplay= true ;
player.load();
});
<?PHP
}else{
?>
function play_song(id, song){
if(id.length < 1) { return; }
if(song.length < 1) { return; }

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
	<?PHP if ($song_dev!='vmix'){ ?>
	document.getElementById("play_"+id).value="Play Pause" + song;
	document.getElementById("stop_"+id).value="Stop Song " + song;
	document.getElementById("stop_"+id).disabled = false;
	document.getElementById("play_"+id).onclick= function(){
	var no=this.id.substr(5);
	var songNo=this.value.substr(10);
		pause_song(no, songNo);
		}
	<?PHP } ?>
	if (resp=="not_live"){
	alert('The meeting is not started yet! Don\'t forget to start it!');
	}else if (resp=="no_space_left"){
	alert('Only 4 inputs at the time. No space to play the song. Please close one of the inputs and try again.');
	}
    }
    }
   
    }
xmlhttp.open("GET","song.php?play=" + song, true);
xmlhttp.send();
}
function play_rand(){
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
	document.getElementById("play_rand").value="Play Pause";
	document.getElementById("stop_rand").disabled = false;
	document.getElementById("play_rand").onclick= function(){
		pause_rand();
		}
    }
    }
    }
xmlhttp.open("GET","song.php?play=rand", true);
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
function stop_rand(){
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
	document.getElementById("play_rand").value = "Play Random";
	document.getElementById("stop_rand").disabled = true;
	document.getElementById("play_rand").onclick= function(){
		play_rand();
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
    document.getElementById("stop_"+id).value="Stop Song " + song;
    }
    }
   
    }
xmlhttp.open("GET","song.php?pause=true", true);
xmlhttp.send();
}
function pause_rand(){
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
<?PHP if ($song_dev!='vmix') { ?>
var link = document.getElementById("play_rand");
	link.onclick= function(){
		play_rand();
		}
<?PHP } ?>
for (var i = 1; i <= 3; ++i) {
var link = document.getElementById("stop_"+i);
	link.onclick= function(){
	var no=this.id.substr(5);
	var songNo=this.value.substr(10);
		stop_song(no, songNo);
		}
}
<?PHP if ($song_dev!='vmix') { ?>
var link = document.getElementById("stop_rand");
	link.onclick= function(){
		stop_rand();
		}
<?PHP
	}
}
?>
</script>
