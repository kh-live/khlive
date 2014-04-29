<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
$selected_date=date("Y",time())."-".date("m",time());
$selected_cong=$_SESSION['cong'];
if(isset($_GET['cong'])){
$selected_cong=$_GET['cong'];
$_SESSION['selected_cong']=$selected_cong;
$selected_date=date("Y",time())."-".date("m",time());
}
if(isset($_GET['date'])){
$selected_date=$_GET['date'];
$_SESSION['selected_date']=$selected_date;
}
if(isset($_SESSION['selected_cong'])) $selected_cong=$_SESSION['selected_cong'];
if(isset($_SESSION['selected_date'])) $selected_date=$_SESSION['selected_date'];
?>
<div id="page">
<script>
function update_date(url){
  window.location="./report?date=" + url;
}
function update_cong(url){
  window.location="./report?cong=" + url;
}
</script>
<h2><?PHP echo $lng['report'].' : '.$selected_cong;?></h2>
<?PHP 
if (strstr($_SESSION['meeting_status'],"live")){
echo 'There is a meeting right now. You can not view the report while the meeting is on. Come back after the meeting.';
}else{
echo 'Date: <select id="date" onchange="javascript:update_date(this.value)">';
	$month=date("m",time());
	$year=date("Y",time());
	$i=0;
	while($i<=12){
	$month_tmp=$month-$i;
	$year_tmp=$year;
	if($month_tmp<=0){
	$month_tmp+=12;
	$year_tmp--;
	}
	if($month_tmp<=9) $month_tmp="0".$month_tmp;
	$date=$year_tmp.'-'.$month_tmp;
	$opt="";
	if ($selected_date==$date) $opt='selected="selected"';
	echo '<option value="'.$date.'" '.$opt.'>'.$month_tmp.'/'.$year_tmp.'</option>';
	$i++;
	}
	echo '</select><br /><br />';
	if ($_SESSION['type']=="root"){
	echo $lng['congregation'].': <select id="cong" onchange="javascript:update_cong(this.value)">';
	$congs=file('./db/cong');
	foreach($congs as $congreg){
	$cong=explode("**",$congreg);
	$opt="";
	if ($selected_cong==$cong[0]) $opt='selected="selected"';
	echo '<option value="'.$cong[0].'" '.$opt.'>'.$cong[0].'</option>';
	}
	echo '</select><br /><br />';
	}
	$file="db/logs-".$selected_cong.'-'.$selected_date;
	if (!file_exists($file)){
	echo 'There is no data for this month. Try another one...';
	}else{
$db=file($file);
$listeners_in=array();
$listeners_out=array();
$answers=array();
$tmp_list=0;
$tmp_txt="";
$in_meeting=0;
$streaming=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	//the meeting wasnt stopped properly
	if ($data[2]=="meeting start" AND $in_meeting==1){
	$total_list=count($listeners_in);
	$total_ans=0;
		if (count($answers)!=0){
			foreach($answers as $tmp){
			$total_ans+=$tmp;
			}
		}
		foreach($listeners_in as $listener=>$time){
	$total_min=round(($listeners_out[$listener]-$listeners_in[$listener])/60)." min";
	echo '<tr><td>'.$listener.'</td><td>'.date("H:i",$listeners_in[$listener]).'</td><td>'.date("H:i",$listeners_out[$listener]).'</td><td>'.$total_min.'</td><td>'.@$answers[$listener].'</td></tr>';
	}
	echo '<tr><td colspan="5">TOTAL LISTENERS : '.$total_list.'</td></tr>
	<tr><td colspan="5">TOTAL Answers : '.$total_ans.'</td></tr>
	<tr><td colspan="5">Stop Time : --</td></tr></table><br />';
	$listeners_in=array();
	$listeners_out=array();
	$answers=array();
	$in_meeting=0;
	}
	//new meeting
	if ($data[2]=="new mount"){
	$streaming=1;
	//the following only works if the meeting was stopped properly. If not, the users will be erased.
	$listeners_in=array();
	$listeners_out=array();
	$answers=array();
	}elseif ($data[2]=="meeting start"){
	$downloads=array();
	$in_meeting=1;
	echo '<table><tr><td colspan="5"><b>Meeting : '.date("d.m.Y",$data[0]).'</b></td></tr><tr><td colspan="5">Start Time : '.date("H:i",$data[0]).'</td></tr>
	<tr><td>User</td><td>start</td><td>stop</td><td>total</td><td>answers</td></tr>';
	//new listener
	}elseif ($data[2]=="new listener" AND $streaming==1){
	if (!isset($listeners_in[$data[3]])) $listeners_in[$data[3]]=$data[0];
	$tmp_list++;
	//new answer
	}elseif ($data[2]=="answer start" AND $in_meeting==1){
		if (!isset($answers[$data[3]])) $answers[$data[3]]=0;
	$answers[$data[3]]+=1;
	//listener quits
	}elseif ($data[2]=="listener left"){
	if ($tmp_list>=1){
	$tmp_list--;
	$listeners_out[$data[3]]=$data[0];
	}
	//end of meeting
		
	}elseif (($data[2]=="meeting stop" OR $data[2]=="mount stopped") AND $in_meeting==1){
	$streaming=0;
	$total_list=count($listeners_in);
	$total_ans=0;
		if (count($answers)!=0){
			foreach($answers as $tmp){
			$total_ans+=$tmp;
			}
		}
		//everybody was gone when the meeting stopped
		if ($tmp_list==0){
		foreach($listeners_in as $listener=>$time){
	$total_min=round(($listeners_out[$listener]-$listeners_in[$listener])/60)." min";
	echo '<tr><td>'.$listener.'</td><td>'.date("H:i",$listeners_in[$listener]).'</td><td>'.date("H:i",$listeners_out[$listener]).'</td><td>'.$total_min.'</td><td>'.@$answers[$listener].'</td></tr>';
	}
	echo '<tr><td colspan="5">TOTAL LISTENERS : '.$total_list.'</td></tr>
	<tr><td colspan="5">TOTAL Answers : '.$total_ans.'</td></tr>
	<tr><td colspan="5">Stop Time : '.date("H:i",$data[0]).'</td></tr></table><br />';
	$listeners_in=array();
	$listeners_out=array();
	$answers=array();
	$in_meeting=0;
		//not all the listeners had left before the end of the meeting
		}else{
		$tmp_txt='<tr><td colspan="5">TOTAL LISTENERS : '.$total_list.'</td></tr>
	<tr><td colspan="5">TOTAL Answers : '.$total_ans.'</td></tr>
	<tr><td colspan="5">Stop Time : '.date("H:i",$data[0]).'</td></tr></table><br />';
		}
	}elseif ($data[2]=="new download" AND $in_meeting!=1){
		if (!isset($downloads[$data[3]])){
	echo '<table><tr><td><b>Meeting downloaded by :</b> '.$data[3].'</td></tr></table><br />';
	$downloads[$data[3]]="ok";
	}
	}
	if ($tmp_list==0 AND $tmp_txt!=""){
	foreach($listeners_in as $listener=>$time){
	$total_min=round(($listeners_out[$listener]-$listeners_in[$listener])/60)." min";
	echo '<tr><td>'.$listener.'</td><td>'.date("H:i",$listeners_in[$listener]).'</td><td>'.date("H:i",$listeners_out[$listener]).'</td><td>'.$total_min.'</td><td>'.@$answers[$listener].'</td></tr>';
	}
	echo $tmp_txt;
	$listeners_in=array();
	$listeners_out=array();
	$answers=array();
	$in_meeting=0;
	$tmp_txt="";
	}
}
if ($in_meeting==1){
	$total_list=count($listeners_in);
	$total_ans=0;
		if (count($answers)!=0){
			foreach($answers as $tmp){
			$total_ans+=$tmp;
			}
		}
		foreach($listeners_in as $listener=>$time){
	$total_min=round(($listeners_out[$listener]-$listeners_in[$listener])/60)." min";
	echo '<tr><td>'.$listener.'</td><td>'.date("H:i",$listeners_in[$listener]).'</td><td>'.date("H:i",$listeners_out[$listener]).'</td><td>'.$total_min.'</td><td>'.@$answers[$listener].'</td></tr>';
	}
	echo '<tr><td colspan="5">TOTAL LISTENERS : '.$total_list.'</td></tr>
	<tr><td colspan="5">TOTAL Answers : '.$total_ans.'</td></tr>
	<tr><td colspan="5">Stop Time : --</td></tr></table><br />';
	$listeners_in=array();
	$listeners_out=array();
	$answers=array();
	$in_meeting=0;
	}
}
}
?>
</div>

