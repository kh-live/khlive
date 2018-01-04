<?PHP
/*
IMPORTANT! DO NOT MODIFY THIS FILE IF YOU WANT TO CUSTOMIZE THE TIMING. 
DO CHANGES ONLY IN TIMING-STANDALONE-TESTING.PHP
THIS FILE IS USED TO GENERATE THE TIMINGS ON USER LISTENING PAGE AND MANAGER MEETING PAGE AS WELL
*/
?>

<div id="meeting_time">
<?PHP
$scripts='';
include "db/config.php";
if ($server_beta=='true') date_default_timezone_set('Africa/Johannesburg');
$temp='';
$meeting_status='No meeting today';
  if ($dh = @opendir($temp_dir)) {
       while (($file = readdir($dh)) !== false) {
		if (strstr($file, 'test_meeting_')){ 
		$temp=implode("",file($temp_dir.$file));
		if ($temp=='live'){
		$cong=str_replace('test_meeting_','',$file);
		$meeting_status=str_replace('_', ' ',$cong).' test meeting is live!';
		}
		}elseif (strstr($file, 'meeting_')){
		$temp=implode("",file($temp_dir.$file));
		if ($temp=='live'){
		$cong=str_replace('meeting_','',$file);
		$meeting_status=str_replace('_', ' ',$cong).' meeting is live!';
		}
		}
	}
}
$todays=(date('G',time()) * 3600) + (date('i',time()) * 60) + date('s',time());
$skip='';
$duration_left='';
$time_vectors=array();
if ($scheduler=='yes'){
	if (file_exists("db/sched")){
	$db=file("db/sched");
	foreach($db as $line){
        $data=explode ("**",$line);
	if ((@$data[4]=='yes') AND (date('D',time())==$data[1])){
	$start_time=explode(':',$data[2]);
	$stop_time=explode(':',$data[3]);
	$start_times=($start_time[0] * 3600) + ($start_time[1] * 60);
	$stop_times=($stop_time[0] * 3600) + ($stop_time[1] * 60);
		if (($todays >= $start_times) AND ($todays < $stop_times)){
			$skip='yes';
			$time_vectors[$start_times-$todays]=$line;
			break;
		}else{
		$time_vectors[$start_times-$todays]=$line;
		}
	}
	}
	if (count($time_vectors)>0){
	if ($skip==''){
	ksort($time_vectors);
		foreach($time_vectors as $key => $line){
		if ($key > 0) {
			$data=explode ("**",$line);
			$start_time=explode(':',$data[2]);
			$stop_time=explode(':',$data[3]);
			break;
		}else{
			$data=explode ("**",$line);
			$start_time=explode(':',$data[2]);
			$stop_time=explode(':',$data[3]);
		}
		}
	}
		$cong=$data[0];
		$timing_id=$data[5];

		$duration = (($stop_time[0] - $start_time[0]) * 3600) + (($stop_time[1] - $start_time[1]) * 60);
		$elapsed=((date('G',time()) - $start_time[0]) * 3600) + ((date('i',time()) - $start_time[1]) * 60) + (date('s',time()));
		//duration left has to be set to the part length if the timing exists
		$duration_left = (($stop_time[0] - date('G',time())) * 3600) + (($stop_time[1] - date('i',time())) * 60) + ((0 - date('s',time())));
		
		if ((date('G',time())>$stop_time[0]) OR ((date('G',time())==$stop_time[0]) AND (date('i',time())>=$stop_time[1]))){
		//that meeting is over
		$meeting_status=str_replace('_',' ',$cong).'\'s meeting ended at '.sprintf('%02d:%02d', $stop_time[0], $stop_time[1]);
		$scripts.='
		clearTimeout(refresh);
		refresh=setTimeout(function(){refreshPage();},(60-'.date('s',time()).')*1000);
		window.testTemp=\'0\';
		';
		}elseif ((date('G',time())<$start_time[0]) OR ((date('G',time())==$start_time[0]) AND (date('i',time())<$start_time[1]))){
		//the meeting will start later today
		$meeting_status=str_replace('_',' ',$cong).'\'s meeting will start at '.sprintf('%02d:%02d', $start_time[0], $start_time[1]);
		$scripts.='
		clearTimeout(refresh);
		refresh=setTimeout(function(){refreshPage();},(60-'.date('s',time()).')*1000);
		';
		}else{
		
		//we are in a meeting
		$part_name="";
		if ($timing_id!="0"){
	
			if (file_exists("db/timings")){
		
				$db0=file("db/timings");
			foreach($db0 as $line0){
			$data0=explode ("**",$line0);

				if ($data0[0]==$timing_id){
				$timings=unserialize($data0[1]);
				$j=1;
				$globaltime=0;
while($j<=50){
if (isset($timings['name'.$j])){
	if (($globaltime<=$elapsed) AND ($elapsed<=$globaltime+($timings['length'.$j]*60))){
		
		//we set the timer with the length that's left for this part
		$duration_left=$globaltime+($timings['length'.$j]*60)-$elapsed;
		if ($duration_left>=0){
		$part_name=$timings['name'.$j].' - '.$timings['length'.$j].'min';
		}
	}
	$globaltime+=($timings['length'.$j]*60);
}
$j++;
}
if ($part_name==""){
$part_name="End of meeting";
}
				}
			}
			}
		}
		$meeting_status='<p style="font-size:0.7em;color:rgba(255,255,255,0.5);">'.str_replace('_',' ',$cong).'\'s meeting is streaming from '.sprintf('%02d:%02d', $start_time[0], $start_time[1]).' to '.sprintf('%02d:%02d', $stop_time[0], $stop_time[1]).'</p>';
		if ($part_name!=""){
		$meeting_status.=" ".$part_name;
		}
		//we dont refresh the page during a meeting only after
		$scripts.='
		clearTimeout(refresh);
		refresh=setTimeout(function(){refreshPage();},('.$duration_left.'+2)*1000);
		';
		

		}
	}
	}
}
	

echo '<div id="meeting_overall">'.$meeting_status.'</div>';
?>
<div id="meeting_clock"><h1>88</h1>:<h1>88</h1>:<h1>88</h1></div>
<div id="meeting_times">
<?PHP
if (isset($timings)){
$seconds=$duration_left;
$hours = floor($seconds / 3600);
$mins = floor($seconds / 60 % 60);
$secs = floor($seconds % 60);
echo '<h1 id="hours">'.sprintf('%02d',$hours).'</h1>:<h1 id="minutes">'.sprintf('%02d',$mins).'</h1>:<h1 id="secondes">'.sprintf('%02d',$secs).'</h1>';
}else{
echo '<h1 id="hours">'.date('H', time()).'</h1>:<h1 id="minutes">'.date('i', time()).'</h1>:<h1 id="secondes">'.date('s', time()).'</h1>';
}
?>
</div>
</div>

<?PHP
if (isset($timings) AND $duration_left>0){
$scripts.='
killIntervals();
window.targetTime=new Date(Date.now()+'.($duration_left*1000).');
window.KhClock.push=setInterval(function(){countdownTarget();},100);
';

}else{
$scripts.='
window.KhClock.push=setInterval(function(){syncClock();},100);
';
}
?>
<script type="text/javascript">
if (typeof window.KhClock === 'undefined') {
var window.KhClock=new Array();
}
if (typeof serverTime === 'undefined') {
var serverTime=<?PHP echo time(); ?>;
}
if (typeof window.delta === 'undefined') {
window.delta = Date.now() - serverTime*1000;
}
if (typeof window.targetTime === 'undefined') {
window.targetTime;
}
function killIntervals(){
for (let value of window.KhClock) {
clearInterval(value);
}
window.KhClock=new Array();
}
function syncClock(){
if (window.testTemp!='1'){
var adjustedTime=new Date(Date.now()-window.delta);
var secondes=adjustedTime.getSeconds();
var minutes=adjustedTime.getMinutes();
var hours=adjustedTime.getHours();
if (("" + secondes).length==1) secondes= "0" + secondes;
if (("" + minutes).length==1) minutes= "0" + minutes;
if (("" + hours).length==1) hours= "0" + hours;
document.getElementById("secondes").innerHTML=secondes;
document.getElementById("minutes").innerHTML=minutes;
document.getElementById("hours").innerHTML=hours;
}else{
killIntervals();
}
}

function countdownTarget(){
window.testTemp='1';
var timeLeft=(window.targetTime.getTime()-Date.now())/1000;
var secondes=Math.floor(timeLeft %60);
var minutes=Math.floor((timeLeft / 60 ) %60);
var hours=Math.floor(timeLeft / 3600);
if (("" + secondes).length==1) secondes= "0" + secondes;
if (("" + minutes).length==1) minutes= "0" + minutes;
if (("" + hours).length==1) hours= "0" + hours;
		if(timeLeft<=0){
		killIntervals();
		document.getElementById("meeting_times").innerHTML="<h1 id=\"hours\">TIMEOUT!</h1>";
		}else{
document.getElementById("secondes").innerHTML=secondes;
document.getElementById("minutes").innerHTML=minutes;
document.getElementById("hours").innerHTML=hours;
}
}

//we refresh every hour if there is no meeting
var refresh=setTimeout(function(){refreshPage();},3600*1000);

<?PHP echo $scripts ; ?>
</script>