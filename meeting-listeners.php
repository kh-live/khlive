<?PHP
	echo '<b>Users live : </b><br /><br />';
	$db=file("db/live_users");
	if (count($db)==0){
	echo 'No live users';
	}else{
	foreach($db as $line){
	$data=explode ("**",$line);
	if($data[2]==$_SESSION['cong']){
	if($data[5]=="normal"){
	if($data[3]=="phone_live"){
	echo '<div class="live_user"><img src="./img/phone1.png" /><br />'.$data[0].' - <a class="stop" href="./meeting-ajax.php?kill=1&user='.$data[0].'">x</a></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	echo '<div class="live_user"><img src="./img/phone_record.png" /><br />'.$data[0].'</div>';
	}else{
	//this is streaming
	echo '<div class="live_user"><h1 class="user_count">'.$data[6].'</h1><img src="./img/comp1.png" /><br />'.$data[1].'</div>';
	}
	}elseif(strstr($data[5],"request")){
	if($data[3]=="phone_live"){
	echo '<div class="live_user"><a href="./answer.php?action=answering&client='.urlencode($data[0]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[1].'"><img src="./img/phone2.png" /></a><br />'.$data[0].'</div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	echo '<div class="live_user"><a href="./answer.php?action=sms_a&client='.$data[1].'&cong='.$data[2].'"><img src="./img/comp2.png" /></a><br />'.$data[1].' <br />(Answer to :'.urldecode($paragraph).')</div>';
	}
	}elseif(strstr($data[5],"answering")){
	if($data[3]=="phone_live"){
	echo '<div class="live_user"><a href="./answer.php?action=stop&client='.urlencode($data[0]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[1].'"><img src="./img/phone3.png" /></a><br />'.$data[0].'</div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	$answer=$tmp[2];
	echo '<div class="live_user"><img src="./img/comp3.png" /><div id="meeting_answer">
	<a href="./answer.php?action=sms_cancel&client='.$data[1].'&cong='.$data[2].'">NOT answered</a> <a href="./answer.php?action=sms_stop&client='.$data[1].'&cong='.$data[2].'">ANSWERED</a>
<b>ANSWER :<br />from : '.$data[1].'<br />to : '.$paragraph.'</b><br />'.urldecode($answer).'</div><br />'.$data[1].'</div>';
	}
	}else{
	//should not happen
	}
	}
	}
	}
?>