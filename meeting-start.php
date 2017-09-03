<?PHP
//we need db/config.php for this to work
if ($meeting_type=="sip"){
	$info="Channel: SIP/".$_SESSION['cong_phone_no']."
MaxRetries: 1
RetryTime: 60
WaitTime: 30
Context: test-menu
Extension: meet_me_".$_SESSION['cong']."_admin
Priority: 1
";
}elseif ($meeting_type=="iax"){
	$info="Channel: IAX2/".$_SESSION['cong_phone_no']."
MaxRetries: 1
RetryTime: 60
WaitTime: 30
Context: test-menu
Extension: meet_me_".$_SESSION['cong']."_admin
Priority: 1
";
}elseif ($meeting_type=="direct"){
	$info="Channel: console/".$server_audio." 
MaxRetries: 1
RetryTime: 60
WaitTime: 30
Context: test-menu
Extension: meet_me_".$_SESSION['cong']."_admin
Priority: 1
";
}
if ($meeting_type!='direct-stream'){
$file=fopen('/tmp/meeting_'.$_SESSION['cong'].'_admin.call','w');
			if(fputs($file,$info)){
			fclose($file);
			rename('/tmp/meeting_'.$_SESSION['cong'].'_admin.call', $asterisk_spool.'outgoing/meeting_'.$_SESSION['cong'].'_admin.call');
			//fixit what if the call fails???
			  if ($meeting_processor!='scheduler'){
				echo 'Starting...<br /><br />';
			  }
			  $_SESSION['meeting_just_started']=1;
			$info=time().'**info**meeting start**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
				
			}
	}
	}else{
	//this is direct-stream
	//we must start a script then log
	if ($stream_type=='mp3'){
	if ($record=='yes'){
	exec("arecord -f S16_LE -r 8000 | ".$lame_bin." --preset cbr ".$bitrate." -b 16 -m m -S - - | ".$ezstream_bin." -c ".$web_server_root."kh-live/config/asterisk-ezstream-".$_SESSION['cong'].".xml > /dev/null &");
	exec('arecord -f S16_LE -r 8000 | '.$lame_bin." -f -b 16 -m m -S - ".$web_server_root."kh-live/records/".$_SESSION['cong'].'-'.date('Ymd',time()).'_'.date('His',time()).'.mp3'." > /dev/null &");
	}else{
	exec("arecord -f S16_LE -r 8000 | ".$lame_bin." --preset cbr ".$bitrate." -b 16 -m m -S - - | ".$ezstream_bin." -c ".$web_server_root."kh-live/config/asterisk-ezstream-".$_SESSION['cong'].".xml > /dev/null &");
	}
	}elseif ($stream_type=='ogg'){
	if ($record=='yes'){
	exec("arecord -f S16_LE -r 8000 | ".$ices_bin." ".$web_server_root."kh-live/config/asterisk-ices-".$_SESSION['cong'].".xml > /dev/null &");
	exec("arecord -f S16_LE -r 8000 | ".$lame_bin.' -f -b 16 -m m -S - '.$web_server_root.'kh-live/records/'.$_SESSION['cong'].'-'.date('Ymd',time()).'_'.date('His',time()).'.mp3'." > /dev/null &");
	}else{
	exec("arecord -f S16_LE -r 8000 | ".$ices_bin." ".$web_server_root."kh-live/config/asterisk-ices-".$_SESSION['cong'].".xml > /dev/null &");
	}
	}else{
	// this is both
	if ($record=='yes'){
	exec("arecord -f S16_LE -r 8000 | ".$ices_bin." ".$web_server_root."kh-live/config/asterisk-ices-".$_SESSION['cong'].".xml > /dev/null &");
	exec("arecord -f S16_LE -r 8000 | ".$lame_bin." --preset cbr ".$bitrate." -b 16 -m m -S - - | ".$ezstream_bin." -c ".$web_server_root."kh-live/config/asterisk-ezstream-".$_SESSION['cong'].".xml > /dev/null &");
	exec('arecord -f S16_LE -r 8000 | '.$lame_bin." -f -b 16 -m m -S - ".$web_server_root."kh-live/records/".$_SESSION['cong'].'-'.date('Ymd',time()).'_'.date('His',time()).'.mp3'." > /dev/null &");
	}else{
	exec("arecord -f S16_LE -r 8000 | ".$ices_bin." ".$web_server_root."kh-live/config/asterisk-ices-".$_SESSION['cong'].".xml > /dev/null &");
	exec("arecord -f S16_LE -r 8000 | ".$lame_bin." --preset cbr ".$bitrate." -b 16 -m m -S - - | ".$ezstream_bin." -c ".$web_server_root."kh-live/config/asterisk-ezstream-".$_SESSION['cong'].".xml > /dev/null &");
	}
	}
		 
			  $_SESSION['meeting_just_started']=1;
			$info=time().'**info**meeting start**'.$_SESSION['cong'].'**'.$_SESSION['user']."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
				if ($meeting_processor!='scheduler'){
					 echo 'Starting...<br /><br />
						<script>
						setTimeout(function(){ window.location= "./meeting-ajax.php"},15000);
						</script>';
				}
	}
?>