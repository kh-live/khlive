<?PHP
	
	if ($server_beta='true'){
	//we generate a list of live users
	$db=array(
	'1**Listener 1 (over internet stream - 10 people listening together)**start_cong**stream-start_cong.ogg**'.time().'**normal**10**',
	'2**Listener 2 (over internet stream - wanting to answer to paragraph 1)**start_cong**stream-start_cong.ogg**'.time().'**request--paragraph 1--This is a test answer by a test listener**1**',
	'3**Listener 3 (over internet stream - answering to paragraph 2)**start_cong**stream-start_cong.ogg**'.time().'**answering--paragraph 2--This is a test answer by a test listener**1**',
	'4**Listener 4 (over VOIP - click on the cross to disconnect user)**start_cong**phone_live**'.time().'**normal****',
	'5**Listener 5 (over VOIP - listening to a recorded meeting)**start_cong**phone_record**'.time().'**normal****',
	'6**Listener 6 (over VOIP - wanting to answer with voice answer - click here to open his microphone)**start_cong**phone_live**'.time().'**request****',
	'7**Listener 7 (over VOIP - answering - click here to close his microphone)**start_cong**phone_live**'.time().'**answering****'
	);
	}else{
	$db=file("db/live_users");
	}
	if (count($db)==0){
	echo 'No live users';
	}else{
	echo '<b style="font-size:1.2em;">Listeners list : </b><br /><br />';
	$user_class='live_user';
	foreach($db as $line){
	
	if ($user_class=='live_user'){
	$user_class='live_user_1';
	}else{
	$user_class='live_user';
	}
	
	$data=explode ("**",$line);
	if($data[2]==$_SESSION['cong']){
	if($data[5]=="normal"){
	if($data[3]=="phone_live"){
	echo '<div class="'.$user_class.'"><img src="./img/phone1.png" /><div class="live_user_name">'.$data[1].' - <a class="stop" href="./meeting-ajax.php?kill=1&user='.$data[1].'">x</a></div></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	echo '<div class="'.$user_class.'"><img src="./img/phone_record.png" /><div class="live_user_name">'.$data[1].'</div></div>';
	}else{
	//this is streaming
	echo '<div class="'.$user_class.'"><h1 class="user_count">'.$data[6].'</h1><img src="./img/comp1.png" /><div class="live_user_name">'.$data[1].'</div></div>';
	}
	}elseif(strstr($data[5],"request")){
	if($data[3]=="phone_live"){
	echo '<div class="'.$user_class.'"><a class="live_user_link" href="./answer.php?action=answering&client='.urlencode($data[1]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[0].'"><img src="./img/phone2.png" /><div class="live_user_name"><b>'.$data[1].'</b></div></a></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	echo '<div class="'.$user_class.'"><a class="live_user_link" href="./answer.php?action=sms_a&client='.$data[1].'&cong='.$data[2].'"><img src="./img/comp2.png" /><div class="live_user_name"><b>'.$data[1].'</b><br /><i style="color:rgba(0,0,0,0.7)">(Answer to :'.urldecode($paragraph).')</i></div></a></div>';
	}
	}elseif(strstr($data[5],"answering")){
	if($data[3]=="phone_live"){
	echo '<div class="'.$user_class.'"><a class="live_user_link" href="./answer.php?action=stop&client='.urlencode($data[1]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[0].'"><img src="./img/phone3.png" /><div class="live_user_name"><b>'.$data[1].'</b></div></a></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	$answer=$tmp[2];
	echo '<div class="'.$user_class.'"><img src="./img/comp3.png" /><div class="live_user_name"><b>'.$data[1].'</b></div>
	<div id="meeting_answer">
	<a href="./answer.php?action=sms_cancel&client='.$data[1].'&cong='.$data[2].'">NOT answered</a> <a href="./answer.php?action=sms_stop&client='.$data[1].'&cong='.$data[2].'">ANSWERED</a>
<b>ANSWER :<br />from : '.$data[1].'<br />to : '.$paragraph.'</b><br />'.urldecode($answer).'</div></div>';
	}
	}else{
	//should not happen
	}
	}
	}
	}
?>