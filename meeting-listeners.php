<?PHP
	
	if ($server_beta=='true'){
	//we generate a list of live users
	$db=array(
	'1**Listener 1 (over internet stream - 10 people listening together)**start_cong**stream-start_cong.ogg**'.time().'**normal**10**1**',
	'2**Listener 2 (over internet stream - wanting to answer to paragraph 1)**start_cong**stream-start_cong.ogg**'.time().'**request--paragraph 1--This is a test answer by a test listener**1**2**',
	'3**Listener 3 (over internet stream - answering to paragraph 2)**start_cong**stream-start_cong.ogg**'.time().'**answering--paragraph 2--This is a test answer. Click on the buttons below to close it.**1**3**',
	'4**Listener 4 (over VOIP - click on the cross to disconnect user)**start_cong**phone_live**'.time().'**normal****4**',
	'5**Listener 5 (over VOIP - listening to a recorded meeting)**start_cong**phone_record**'.time().'**normal****5**',
	'6**Listener 6 (over VOIP - wanting to answer with voice answer - click here to open his microphone)**start_cong**phone_live**'.time().'**request****6**',
	'7**Listener 7 (over VOIP - answering - click here to close his microphone)**start_cong**phone_live**'.time().'**answering****7**',
	'8**Listener (over stream - with a duplicated name)**start_cong**stream-start_cong.ogg**'.time().'**normal****8**',
	'9**Listener (over stream - with a duplicated name)**start_cong**stream-start_cong.ogg**'.time().'**normal****9**',
	'10**Listener (over stream - with a connection problem)**start_cong**stream-start_cong.ogg**'.time().'**normal****10**',
	'11**Listener (over stream - with a connection problem)**start_cong**stream-start_cong.ogg**'.time().'**normal****10**'
	);
	}else{
	$db=file("db/live_users");
	}
	if (count($db)==0){
	echo 'No live users';
	}else{
	echo '<b style="font-size:1.2em;">Listeners list : </b><br /><br />';
	$output='';
	$important_output='';
	$most_important_output='';
	$user_class='live_user_1';
	foreach($db as $line){
	
	$data=explode ("**",$line);
	$show_card='yes';
	//this is a mix of edcast listener and the username
	$temp_user=md5($data[7].$data[1]);
	if (isset($$temp_user)){
	//this is a duplicated user
	//we mustnt display a card for this user
	//this can potentially prevent an answering user, but it's better than floading the screen with multiple identical cards
	$show_card='no';
	//we must also show that the user is having connection problems by adding a red frame around the already existing card
	$output.='<script>
	document.getElementById("'.$temp_user.'").style.border="2px solid red";
	document.getElementById("'.$temp_user.'").innerHTML+="<i style=\"color:red\">This user seems to be having connection problems</i>";
	</script>';
	}
	//we lookup the full name of each listener and store it in session to avoid repeated calls to user db.
	if (!isset($_SESSION['kh_listener'.str_replace(' ','_',$data[1])])){
	$db2=file("db/users");
		foreach($db2 as $line2){
		$data2=explode ("**",$line2);
		if ($data2[0]==$data[1]) $_SESSION['kh_listener'.str_replace(' ','_',$data[1])]=$data2[2];
		}
	}
	//if for some reason we can't find it, we default to the name given to edcast
	if (!isset($_SESSION['kh_listener'.str_replace(' ','_',$data[1])])) $_SESSION['kh_listener'.str_replace(' ','_',$data[1])]=$data[1];
	
	if($data[2]==$_SESSION['cong']){
	if ($show_card=='yes'){
	//we set this variable to avoid duplicated listeners in the list
	$$temp_user='ok';
	if($data[5]=="normal"){
	if($data[3]=="phone_live"){
	$output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><img src="./img/phone1.png" /><div class="live_user_name">'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].' - <a class="stop" href="./meeting-ajax.php?kill=1&user='.$data[1].'">x</a></div></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	$output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><img src="./img/phone_record.png" /><div class="live_user_name">'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].'</div></div>';
	}else{
	//this is streaming
	$output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><h1 class="user_count">'.$data[6].'</h1><img src="./img/comp1.png" /><div class="live_user_name">'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].'</div></div>';
	}
	}elseif(strstr($data[5],"request")){
	if($data[3]=="phone_live"){
	$important_output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><a class="live_user_link" href="./answer.php?ajax_meeting_page=ok&action=answering&client='.urlencode($data[1]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[0].'"><img src="./img/phone2.png" /><div class="live_user_name"><b>'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].'</b></div></a></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	$important_output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><a class="live_user_link" href="./answer.php?ajax_meeting_page=ok&action=sms_a&client='.$data[1].'&cong='.$data[2].'"><img src="./img/comp2.png" /><div class="live_user_name"><b>'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].'</b><br /><i style="color:rgba(0,0,0,0.7)">(Answer to :'.urldecode($paragraph).')</i></div></a></div>';
	}
	}elseif(strstr($data[5],"answering")){
	if($data[3]=="phone_live"){
	$most_important_output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><a class="live_user_link" href="./answer.php?ajax_meeting_page=ok&action=stop&client='.urlencode($data[1]).'&cong='.$data[2].'&type='.$data[3].'&conf='.$data[0].'"><img src="./img/phone3.png" /><div class="live_user_name"><b>'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].'</b></div></a></div>';
	}elseif($data[3]=="phone_record"){
	//listening to a recording while the meeting is on... shouldnt happen
	// cant answer
	}else{
	//this is streaming
	$tmp=explode("--",$data[5]);
	$paragraph=$tmp[1];
	$answer=$tmp[2];
	$most_important_output.= '<div class="'.$user_class.'" id="'.$temp_user.'"><img src="./img/comp3.png" /><div class="live_user_name"><b>'.$_SESSION['kh_listener'.str_replace(' ','_',$data[1])].'</b></div>
	<div class="meeting_answer">
	<b>ANSWER :<br />to : '.$paragraph.'</b><br />'.urldecode($answer).'<br />
	<a href="./answer.php?ajax_meeting_page=ok&action=sms_cancel&client='.$data[1].'&cong='.$data[2].'">NOT answered</a> <a href="./answer.php?ajax_meeting_page=ok&action=sms_stop&client='.$data[1].'&cong='.$data[2].'">ANSWERED</a></div></div>';
	}
	}else{
	//should not happen
	}
	}
	}
	}
	echo $most_important_output;
	echo $important_output;
	echo $output;
	}
?>