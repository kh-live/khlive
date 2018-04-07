<?PHP
$tmp_skip='no';
$gen_version='2.3.5';//gen_version leave this comment it's used in auto_update
$max_song_no=154;
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if (isset($_GET['action'])){
	if ($_GET['action']=="ok"){
	ob_start();
	//we need to make astdatadir configurable for PI or alpine
?>
[directories]
astetcdir => /etc/asterisk
astmoddir => /usr/lib/asterisk/modules
astvarlibdir => /var/lib/asterisk
astdbdir => /var/lib/asterisk
astkeydir => /var/lib/asterisk
astdatadir => /usr/share/asterisk
astagidir => /usr/share/asterisk/agi-bin
astspooldir => /var/spool/asterisk
astrundir => /var/run/asterisk
astlogdir => /var/log/asterisk
astsbindir => /usr/sbin

[options]
;verbose = 3
;debug = 3
;alwaysfork = yes		; Same as -F at startup.
;nofork = yes			; Same as -f at startup.
;quiet = yes			; Same as -q at startup.
;timestamp = yes		; Same as -T at startup.
;execincludes = yes		; Support #exec in config files.
;console = yes			; Run as console (same as -c at startup).
;highpriority = yes		; Run realtime priority (same as -p at
				; startup).
;initcrypto = yes		; Initialize crypto keys (same as -i at
				; startup).
;nocolor = yes			; Disable console colors.
;dontwarn = yes			; Disable some warnings.
;dumpcore = yes			; Dump core on crash (same as -g at startup).
;languageprefix = yes		; Use the new sound prefix path syntax.
;internal_timing = yes
;systemname = my_system_name	; Prefix uniqueid with a system name for
				; Global uniqueness issues.
;autosystemname = yes		; Automatically set systemname to hostname,
				; uses 'localhost' on failure, or systemname if
				; set.
;mindtmfduration = 80		; Set minimum DTMF duration in ms (default 80 ms)
				; If we get shorter DTMF messages, these will be
				; changed to the minimum duration
;maxcalls = 10			; Maximum amount of calls allowed.
;maxload = 0.9			; Asterisk stops accepting new calls if the
				; load average exceed this limit.
;maxfiles = 1000		; Maximum amount of openfiles.
;minmemfree = 1			; In MBs, Asterisk stops accepting new calls if
				; the amount of free memory falls below this
				; watermark.
;cache_record_files = yes	; Cache recorded sound files to another
				; directory during recording.
;record_cache_dir = /tmp	; Specify cache directory (used in conjunction
				; with cache_record_files).
;transmit_silence = yes		; Transmit silence while a channel is in a
				; waiting state, a recording only state, or
				; when DTMF is being generated.  Note that the
				; silence internally is generated in raw signed
				; linear format. This means that it must be
				; transcoded into the native format of the
				; channel before it can be sent to the device.
				; It is for this reason that this is optional,
				; as it may result in requiring a temporary
				; codec translation path for a channel that may
				; not otherwise require one.
;transcode_via_sln = yes	; Build transcode paths via SLINEAR, instead of
				; directly.
;runuser = apache		; The user to run as.
;rungroup = apache		; The group to run as.
;lightbackground = yes		; If your terminal is set for a light-colored
				; background.
;forceblackbackground = yes     ; Force the background of the terminal to be 
                                ; black, in order for terminal colors to show
                                ; up properly.
;defaultlanguage = en           ; Default language
documentation_language = en_US	; Set the language you want documentation
				; displayed in. Value is in the same format as
				; locale names.
;hideconnect = yes		; Hide messages displayed when a remote console
				; connects and disconnects.
;lockconfdir = no		; Protect the directory containing the
				; configuration files (/etc/asterisk) with a
				; lock.
;stdexten = gosub		; How to invoke the extensions.conf stdexten.
				; macro - Invoke the stdexten using a macro as
				;         done by legacy Asterisk versions.
				; gosub - Invoke the stdexten using a gosub as
				;         documented in extensions.conf.sample.
				; Default gosub.
live_dangerously = no		; Enable the execution of 'dangerous' dialplan
				; functions from external sources (AMI,
				; etc.) These functions (such as SHELL) are
				; considered dangerous because they can allow
				; privilege escalation.
				; Default yes, for backward compatability.

; Changing the following lines may compromise your security.
;[files]
;astctlpermissions = 0660
;astctlowner = root
;astctlgroup = apache
;astctl = asterisk.ctl

[compat]
pbx_realtime=1.6
res_agi=1.6
app_set=1.6
<?PHP
        $message = ob_get_clean();
$fichier = fopen('./config/asterisk.conf', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
            fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	ob_start();
?>
; extensions.conf - the Asterisk dial plan
;
; Static extension configuration file, used by
; the pbx_config module. This is where you configure all your
; inbound and outbound calls in Asterisk.
;
; This configuration file is reloaded
; - With the "dialplan reload" command in the CLI
; - With the "reload" command (that reloads everything) in the CLI

;
; The "General" category is for certain variables.
;
[general]
static=yes
writeprotect=no
clearglobalvars=no

[default]
exten => 1234,1,Goto(test-menu,start,1)

[from-sip-external]
exten => meeting,1,Goto(test-menu,start,1)

[from-trunk]
exten => s,1,Goto(test-menu,start,1)

[test-menu]
exten => start,1,Set(LISTENED_TO_RECORD=0)
 same => n,GotoIf($["${DIALSTATUS}" = "ANSWER"]?READID)
 same => n,Answer
 same => n,Wait(1)
 same => n,Set(IDCOUNT=0)
 same => n,GotoIf($[${ISNULL(${CALLERID(num)})}]?NAME)
 same => n,Set(LASTLOGIN=${DB(LASTLOGIN/${CALLERID(num)})})
 same => n,Goto(ID_CHECKED)
 same => n(NAME),Set(LASTLOGIN=${DB(LASTLOGIN/${CALLERID(name)})})
 same => n(ID_CHECKED),GotoIf($[${ISNULL(${LASTLOGIN})}]?WELCOME)
 same => n,GotoIf($[${EPOCH} - ${LASTLOGIN} > 1200]?WELCOME)
 same => n,Set(LASTLOGIN_CONG=${DB(LASTLOGIN_CONG/${CALLERID(num)})})
 same => n,GotoIf($[${ISNULL(${LASTLOGIN_CONG})}]?WELCOME)
 same => n,Set(LASTLOGIN_PIN=${DB(LASTLOGIN_PIN/${CALLERID(num)})})
 same => n,GotoIf($[${ISNULL(${LASTLOGIN_PIN})}]?WELCOME)
 same => n, Goto(${LASTLOGIN_CONG},1)
 same => n(RECONNECT),Set(TEST=${DB_DELETE(LASTLOGIN/${CALLERID(num)})})
 same => n,Set(TEST=${DB_DELETE(LOGGEDIN/${PIN})})
 same => n,Set(TEST=${DB_DELETE(LASTLOGIN_CONG/${CALLERID(num)})})
 same => n,Set(TEST=${DB_DELETE(LASTLOGIN_PIN/${CALLERID(num)})})
 same => n,Set(LASTLOGIN=${DB(LASTLOGIN/${CALLERID(num)})})
 same => n,Set(LASTLOGIN_CONG=${DB(LASTLOGIN_CONG/${CALLERID(num)})})
 same => n,Set(LASTLOGIN_PIN=${DB(LASTLOGIN_PIN/${CALLERID(num)})})
 same => n(WELCOME),Playback(grg/welcome)
 same => n(READID),Playback(grg/please_enter_id)
 same => n, WaitExten(10)
 
exten => grg-id,1, Set(MEETING_STARTED_BY_ADMIN=0)
 same => n,Set(PINCOUNT=0)
 same => n,GotoIf($[${ISNULL(${LASTLOGIN_PIN})}]?START)
 same => n,Set(PIN=${LASTLOGIN_PIN})
 same => n,Goto(CHECK)
 same => n(START),GotoIf($["${DIALSTATUS}" = "ANSWER"]?READPIN)
 same => n,Answer
 same => n,Wait(1)
 same => n(READPIN),Read(PIN,grg/please_enter_pin,,,,15)
 same => n,GotoIf($[x${PIN} = x${ADMIN_PIN}]?ADMIN)
 same => n(CHECK),Set(GRG_USER=${DB(${CURRENT_CONG}/${PIN})})
 same => n,Set(GRG_LOGGEDIN=${DB(LOGGEDIN/${PIN})})
 same => n,GotoIf($[${ISNULL(${GRG_USER})}]?BADTRY)
 same => n,GotoIf($[${ISNULL(${GRG_LOGGEDIN})}]?LOGIN)
 same => n,Playback(grg/pin_used)
  same => n,Set(PIN=0)
 same => n,Goto(quit,1)
 same => n(LOGIN),Set(DB(LOGGEDIN/${PIN})=${CALLERID(num)})
 same => n,Set(DB(LASTLOGIN/${CALLERID(num)})=${EPOCH})
 same => n,Set(DB(LASTLOGIN_CONG/${CALLERID(num)})=${CURRENT_CONF})
 same => n,Set(DB(LASTLOGIN_PIN/${CALLERID(num)})=${PIN})
 same => n,Set(CALLERID(name)=${GRG_USER})
 same => n,Goto(grg-pre,2)
 same => n(BADTRY),Set(PINCOUNT=$[${PINCOUNT}+1])
 same => n,GotoIf($[${PINCOUNT}>2]?wrongpass,1)
 same => n,Playback(grg/wrong_pin)
 same => n,Goto(READPIN)
 same => n(ADMIN),Goto(grg-meetme,start,ADMIN)

exten => grg-pre,1,Wait(1)
 same => n,Read(NB,grg/press_all,1)
 same =>  n,GotoIf($[x${NB} = x1]?LIVE)
 same =>  n,GotoIf($[x${NB} = x2]?OLD)
 same =>  n,GotoIf($[x${NB} = x0]?start,RECONNECT)
 same => n,Goto(2)
 same => n(LIVE),Goto(grg-meetme,start,USER)
 same => n(OLD),Set(TESTREC=${DB(${CURRENT_CONG}/admin)})
 same => n,GotoIf($[${ISNULL(${TESTREC})}]?NOREC)
 same => n,Playback(grg/meeting_no_listen)
 same => n,Playback(grg/please_choose_opt1)
 same => n, Goto(2)
 same => n(NOREC),Playback(grg/press_star)
 same => n,Set(LISTENED_TO_RECORD=1)
 same => n,TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/listener_joined.php?action=phone_add&type=phone_record&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}")
 same => n,ControlPlayback(<?PHP echo $asterisk_spool ;?>meetme/${CURRENT_CONG}_latest,60000,#,*,,0)
 same => n,Set(TESTREC=${DB(${CURRENT_CONG}/admin)})
 same => n,GotoIf($[${ISNULL(${TESTREC})}]?END_REC)
 same => n,TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/listener_left.php?action=phone_remove&type=phone_record&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}")
 same => n,Playback(grg/new_meeting_no_record)
 same => n,Playback(grg/please_choose_opt1)
 same => n, Goto(2)
 same => n(END_REC),Playback(grg/thanks_listening)
 same => n, Goto(quit,1)

exten => i,1,WaitExten(5)
 same => n,Set(IDCOUNT=$[${IDCOUNT}+1])
 same => n,GotoIf($[${IDCOUNT}>2]?quit,1)
 same => n,Playback(grg/please_type_id_correctly)
 same => n,Wait(1)
 same => n,Goto(start,READID)

exten => t,1,Set(IDCOUNT=$[${IDCOUNT}+1])
 same => n,GotoIf($[${IDCOUNT}>2]?quit,1)
 same => n,Playback(grg/please_type_id_correctly)
 same => n,Wait(1)
 same => n,Goto(start,READID)

;this extension is only to catch things after the call has been hung up -> user is gone cant ear anything
exten => h,1,GotoIf($[${ISNULL(${GRG_USER})}]?NOTLOGGEDIN)
 same => n,Set(TEST=${DB_DELETE(LOGGEDIN/${PIN})})
 same => n, GotoIf($[${LISTENED_TO_RECORD}=1]?RECORDQUIT)
 same => n(NOTLOGGEDIN),Hangup
 same => n(RECORDQUIT),TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/listener_left.php?action=phone_remove&type=phone_record&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}")
 same => n,Hangup
 
exten => wrongpass,1,Playback(grg/wrong_pin_3x)
 same =>n, Wait(1)
 same => n,Playback(grg/goodbye)
 same => n,Hangup
 
exten => quit,1,Playback(grg/goodbye)
 same => n,Hangup
 
[grg-meetme]
exten => start,1,Wait(1)
 same => n(ADMIN), Set(MEETME_OPTS=aAwqM)
 same => n,GotoIf($[${LEN(${DB(Testing_0/${CURRENT_CONG})})}>0]?SKIP_REC)
 same => n, Set(MEETME_OPTS=aAwqMr)
 same => n(SKIP_REC),Set(MEETME_ROOMNUM=${CURRENT_CONF})
 same => n,Set(MEETME_RECORDINGFORMAT=wav)
 same => n, Set(MEETME_RECORDINGFILE=${CURRENT_CONG}_latest)
 same => n, Set(MEETING_STARTED_BY_ADMIN=1)
 same => n,Set(CONFPIN=${ADMIN_PIN})
 same => n, Set(ADMINDB=${DB(${CURRENT_CONG}/admin)})
 same => n,Set(ADMIN_KICKED_OUT=0)
 same => n, GotoIf($[${ISNULL(${ADMINDB})}]?NOADMIN)
 ;we dont want to play those sounds they might be heard at the meeting
 ;same => n,Playback(grg/admin_meeting)
 ;same => n,Playback(grg/goodbye)
 same => n,Set(ADMIN_KICKED_OUT=1)
 same => n, Hangup()
 same => n(NOADMIN), Set(DB(${CURRENT_CONG}/admin)=1)
  same => n,TrySystem(cp <?PHP echo $web_server_root ;?>kh-live/config/stream_${CURRENT_CONG}.call /tmp/)
 same => n,TrySystem(mv /tmp/stream_${CURRENT_CONG}.call <?PHP echo $asterisk_spool ;?>outgoing/)
 same => n,TrySystem(echo live > <?PHP echo $temp_dir ;?>meeting_${CURRENT_CONG})
 same => n,Goto(STARTMEETME,1)
 same => n(USER),Set(ADMINDB=${DB(${CURRENT_CONG}/admin)})
 same => n,Set(CONFPIN=${USER_PIN})
 same => n,Set(MEETME_ROOMNUM=${CURRENT_CONF})
 same => n,GotoIf($[${ISNULL(${ADMINDB})}]?NOTREADY)
 same => n,Set(MEETME_OPTS=wqMmX)
 same => n,Playback(grg/meeting_start)
 same => n,Playback(grg/press_4)
 same => n,TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/listener_joined.php?action=phone_add&type=phone_live&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}&confid=${CURRENT_CONF}")
 same => n,Goto(STARTMEETME,1)
 same => n(NOTREADY),Playback(grg/please_wait)
 same => n,Wait(5)
 same => n,Playback(grg/automatic_connect)
 same => n,Wait(5)
 same => n,Goto(USER)

exten =>h,1,GotoIf($[${MEETING_STARTED_BY_ADMIN}=1]?ADMINQUIT)
 same => n,Set(TEST=${DB_DELETE(LOGGEDIN/${PIN})})
 same => n,TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/listener_left.php?action=phone_remove&type=phone_live&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}")
 same => n,Hangup()
 same => n(ADMINQUIT),GotoIf($[${ADMIN_KICKED_OUT}=1]?ADMIN_KICK_OUT)
 same => n,Set(TEST=${DB_DELETE(${CURRENT_CONG}/admin)})
 same => n,GotoIf($[${LEN(${DB_DELETE(Testing_0/${CURRENT_CONG})})}>0]?SKIP_RECORD)
 same => n,TrySystem(<?PHP echo $lame_bin ;?> -f -b 16 -m m -S <?PHP echo $asterisk_spool ;?>meetme/${CURRENT_CONG}_latest.wav <?PHP echo $web_server_root ;?>kh-live/records/${CURRENT_CONG}-${STRFTIME(${NOW},,%Y%m%d)}_${STRFTIME(${NOW},,%H%M%S)}.mp3)
 same => n(SKIP_RECORD),TrySystem(echo down > <?PHP echo $temp_dir ;?>meeting_${CURRENT_CONG})
 same => n,Hangup()
 same => n(ADMIN_KICK_OUT),Set(ADMIN_KICKED_OUT=0)
 same => n,Hangup()

exten => STARTMEETME,1,Set(CHANNEL(musicclass)=grg-music)
 same => n,MeetMe(${MEETME_ROOMNUM},${MEETME_OPTS},${CONFPIN})
 same => n,Hangup()

 exten => killpin,1,Playback(grg/not_authorised)
 same =>n, Wait(1)
 same => n,Playback(grg/goodbye)
 same => n,Hangup

exten => 4,1,TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/answer.php?action=request&type=phone_live&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}")
 same =>n,Playback(grg/give_answer)
 same => n,Goto(STARTMEETME,1)

exten => 6,1,TrySystem(wget -q -O /dev/null "http://<?PHP echo $server_in ;?>/kh-live/answer.php?action=cancel&type=phone_live&cong=${CURRENT_CONG}&client=${URIENCODE(${GRG_USER})}")
 same => n,Playback(grg/request_stop)
 same => n,Goto(STARTMEETME,1)
 
<?PHP
	$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		$cong_name=$data[0];
		$cong_id=$data[1];
		$meetme_admin_pin=$data[2];
		$meetme_user_pin=$data[3];
		$phone_no=$data[4];
		$voip_type=$data[5]; //none/sip/iax
		$stream=$data[6]; //yes/no
		$stream_type=$data[7]; //ogg/mp3
		$voip_password=$data[8]; //rand 16char
		$trunk=$data[9]; //yes/no
		$record=$data[10]; //yes/no
		$answer=$data[11]; //yes/no
		if ($stream_type=="mp3"){
$tmp_type="EAGI(".$web_server_root."kh-live/config/mp3stream-".$cong_name.".sh)";
}else{
$tmp_type="ICES(".$web_server_root."kh-live/config/asterisk-ices-".$cong_name.".xml)";
}
?>
 ;<?PHP echo $cong_name; ?>-start
[test-menu]
exten => <?PHP echo $cong_id; ?>,1,Playback(grg/<?PHP echo $cong_name; ?>)
 same => n,Set(CURRENT_CONG=<?PHP echo $cong_name; ?>)
 same => n,Set(CURRENT_CONF=<?PHP echo $cong_id; ?>)
 same => n,Set(ADMIN_PIN=<?PHP echo $meetme_admin_pin; ?>)
 same => n,Set(USER_PIN=<?PHP echo $meetme_user_pin; ?>)
 same => n,Goto(grg-id,1)
exten => ices_<?PHP echo $cong_name; ?>,1,Answer()
 same => n,Set(LISTENED_TO_RECORD=0)
 same => n,Wait(1)
 same => n,<?PHP echo $tmp_type; ?> 
 same => n,Hangup()
exten => meet_me_<?PHP echo $cong_name; ?>,1,Answer()
 same => n,Set(LISTENED_TO_RECORD=0)
 same => n,Meetme(<?PHP echo $cong_id; ?>,qlMx,<?PHP echo $meetme_user_pin; ?>)
 same => n,Hangup()
exten => meet_me_<?PHP echo $cong_name; ?>_admin,1,Answer()
 same => n,TrySystem(rm <?PHP echo$asterisk_spool;?>outgoing/meeting_<?PHP echo $cong_name; ?>_admin.call)
 same => n,Set(LISTENED_TO_RECORD=0)
 same => n,Set(CURRENT_CONG=<?PHP echo $cong_name; ?>)
 same => n,Set(CURRENT_CONF=<?PHP echo $cong_id; ?>)
 same => n,Set(ADMIN_PIN=<?PHP echo $meetme_admin_pin; ?>)
 same => n,Goto(grg-meetme,start,ADMIN)
 same => n,Hangup()
exten=> test_meeting_<?PHP echo $cong_name; ?>,1,Answer()
 same => n,Set(CURRENT_CONG=<?PHP echo $cong_name; ?>)
 same => n,Set(DB(Testing_0/<?PHP echo $cong_name; ?>)=1)
 same => n(SOUND),Playback(grg/automatic_connect)
 same => n,Wait(10)
 same => n,Goto(SOUND)
 ;<?PHP echo $cong_name; ?>-stop
  <?PHP
  }
      $message = ob_get_clean();
$fichier = fopen('./config/extensions_custom.conf', 'w');
            if (fwrite($fichier, $message)){
	    exec($asterisk_bin.' -rx "dialplan reload"');
            echo "File saved successfully<br />" ;
            fclose ($fichier);
	    }else{
	    // error saving
	    }
	    	ob_start();
		echo "[rooms]\n";
	   $db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		$cong_name=$data[0];
		$cong_id=$data[1];
		$meetme_admin_pin=$data[2];
		$meetme_user_pin=$data[3];
		$phone_no=$data[4];
		$voip_type=$data[5]; //none/sip/iax
		$stream=$data[6]; //yes/no
		$stream_type=$data[7]; //ogg/mp3
		$voip_password=$data[8]; //rand 16char
		$trunk=$data[9]; //yes/no
		$record=$data[10]; //yes/no
		$answer=$data[11]; //yes/no 
	    echo 'conf => '.$cong_id.",".$meetme_user_pin.",".$meetme_admin_pin."\n";
	    }
	          $message = ob_get_clean();
$fichier = fopen('./config/meetme.conf', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	    	    	ob_start();
?>[general]

[logfiles]
console => notice,warning,error,dtmf
messages => notice,warning,error
<?PHP
	          $message = ob_get_clean();
$fichier = fopen('./config/logger.conf', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	    ob_start();
?>#!/bin/sh
#FreeDNS updater script
<?PHP if ($scheduler=="yes") echo 'wget -q -O /dev/null http://'.$server_in.'/kh-live/meeting-sched.php'; ?>

<?PHP if ($auto_ppp=="yes") echo 'ifup ppp0 1>&- 2>&-'; ?>

<?PHP if ($auto_cron=="yes") echo 'cp -u "'.$web_server_root.'kh-live/config/cron" "/etc/cron.d/khlive"'; ?>

<?PHP if ($auto_gov=="yes") echo 'echo performance > /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor'; ?>

<?PHP if ($auto_dns=="yes") {
?>
UPDATEURL="http://freedns.afraid.org/dynamic/update.php?<?PHP echo $moo_key ; ?>"
DOMAIN="<?PHP echo $moo_adr ; ?>"

registered=$(nslookup $DOMAIN|tail -n2|grep A|sed s/[^0-9.]//g)

  current=$(wget -q -O - http://kh-live.co.za/ip.php|sed s/[^0-9.]//g)
       [ "$current" != "$registered" ] && {
wget -q -O /dev/null $UPDATEURL
           }
	   
<?PHP }
if ($auto_khlive=="yes") echo 'wget -q -O /dev/null http://'.$server_in.'/kh-live/update_ip.php'; ?>

<?PHP
	          $message = ob_get_clean();
$fichier = fopen('./config/update.sh', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	     ob_start();
?>*/5 * * * * root <?PHP echo $web_server_root; ?>kh-live/config/update.sh
<?PHP
if (@$video_dowloader=="yes"){
?>5 0 * * * root <?PHP echo $web_server_root; ?>kh-live/config/downloader.sh
<?PHP
}
if (@$auto_stop=="yes"){
?>55 23 * * * root wget -q -O /dev/null http://<?PHP echo $server_in; ?>/kh-live/auto_stop.php
<?PHP
}
//it is very important to finish the cron file with a new line (otherwise it is not executed by cron)
	          $message = ob_get_clean();
$fichier = fopen('./config/cron', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	    	    	    	ob_start();
?>[grg-music]
mode=files
directory=/var/lib/asterisk/moh/grg-music/
sort=alpha
<?PHP
	          $message = ob_get_clean();
$fichier = fopen('./config/musiconhold.conf', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	    	    	    	    	ob_start();
?>;
[modules]
autoload=no

load => res_adsi.so               ; ADSI Resource                            
;load=res_agi.so                ; Asterisk Gateway Interface (AGI)         
;load=res_config_odbc.so        ; ODBC Configuration                       ----
;load=res_crypto.so             ; Cryptographic Digital Signatures         
;load=res_features.so           ; Call Parking Resource                    ----
;load=res_indications.so        ; Indications Configuration                
;load=res_monitor.so            ; Call Monitoring Resource                 
load => res_musiconhold.so        ; Music On Hold Resource                   
;load=res_odbc.so               ; ODBC Resource         ---                   
load => cdr_csv.so                ; Comma Separated Values CDR Backend       
;load=cdr_manager.so            ; Asterisk Call Manager CDR Backend        
;load=cdr_odbc.so               ; ODBC CDR Backend                         
;load=cdr_pgsql.so              ; PostgreSQL CDR Backend                   
;load=chan_agent.so             ; Agent Proxy Channel
<?PHP if ($server_audio=="alsa") { ?>
load => chan_alsa.so              ; ALSA Console Channel Driver
<?PHP } ?>
load => chan_iax2.so              ; Inter Asterisk eXchange (Ver 2)          
load => chan_local.so             ; Local Proxy Channel                      
;load=chan_mgcp.so              ; Media Gateway Control Protocol (MGCP)    
;load=chan_modem.so             ; Generic Voice Modem Driver               
;load=chan_modem_aopen.so       ; A/Open (Rockwell Chipset) ITU-2 VoiceMod 
;load=chan_modem_bestdata.so    ; BestData (Conexant V.90 Chipset) VoiceMo 
;load=chan_modem_i4l.so         ; ISDN4Linux Emulated Modem Driver
<?PHP if ($server_audio=="dsp") { ?>
load => chan_oss.so               ; OSS Console Channel Driver
<?PHP } ?>
;load=chan_phone.so             ; Linux Telephony API Support              
load => chan_sip.so               ; Session Initiation Protocol (SIP)        
;load=chan_skinny.so            ; Skinny Client Control Protocol (Skinny)  
;load=chan_zap.so               ; Zapata Telephony w/PRI                   
;load=codec_a_mu.so             ; A-law and Mulaw direct Coder/Decoder     
;load=codec_adpcm.so            ; Adaptive Differential PCM Coder/Decoder  
;load=codec_alaw.so             ; A-law Coder/Decoder                      
;load=codec_g726.so             ; ITU G.726-32kbps G726 Transcoder         
load => codec_gsm.so              ; GSM/PCM16 (signed linear) Codec Translat 
load => codec_ilbc.so             ; iLBC/PCM16 (signed linear) Codec Transla 
;load=codec_lpc10.so            ; LPC10 2.4kbps (signed linear) Voice Code 
;load=codec_speex.so            ; Speex/PCM16 (signed linear) Codec Transl 
load => codec_ulaw.so             ; Mu-law Coder/Decoder                     
;load=format_g726.so            ; Raw G.726 (16/24/32/40kbps) data         
;load=format_g729.so            ; Raw G729 data                            
load => format_gsm.so             ; Raw GSM data                             
;load=format_h263.so            ; Raw h263 data                            
load => format_ilbc.so            ; Raw iLBC data                            
;load=format_jpeg.so            ; JPEG (Joint Picture Experts Group) Image 
load => format_pcm.so             ; Raw uLaw 8khz Audio support (PCM)        
;load=format_pcm_alaw.so        ; Raw aLaw 8khz PCM Audio support          
;load=format_sln.so             ; Raw Signed Linear Audio support (SLN)    
;load=format_vox.so             ; Dialogic VOX (ADPCM) File Format         
load => format_wav.so             ; Microsoft WAV format (8000hz Signed Line 
load => format_wav_gsm.so         ; Microsoft WAV format (Proprietary GSM)   
;load=app_adsiprog.so           ; Asterisk ADSI Programming Application    
;load=app_alarmreceiver.so      ; Alarm Receiver for Asterisk              
;load=app_authenticate.so       ; Authentication Application               
;load=app_cdr.so                ; Make sure asterisk doesn't save CDR for  
;load=app_chanisavail.so        ; Check if channel is available            
load => app_controlplayback.so    ; Control Playback Application             
;load=app_cut.so                ; Cuts up variables                        
load => app_db.so                 ; Database access functions for Asterisk e 
load => app_dial.so               ; Dialing Application                      
;load=app_directory.so          ; Extension Directory                      
;load=app_disa.so               ; DISA (Direct Inward System Access) Appli 
;load=app_echo.so               ; Simple Echo Application                  
;load=app_enumlookup.so         ; ENUM Lookup                              
;load=app_eval.so               ; Reevaluates strings                      
;load=app_exec.so               ; Executes applications                    
;load=app_festival.so           ; Simple Festival Interface                
;load=app_flash.so              ; Flash zap trunk application              
;load=app_forkcdr.so            ; Fork The CDR into 2 seperate entities.   
;load=app_getcpeid.so           ; Get ADSI CPE ID                          
;load=app_groupcount.so         ; Group Management Routines                
;load=app_hasnewvoicemail.so    ; Indicator for whether a voice mailbox ha 
load => app_ices.so               ; Encode and Stream via icecast and ices   
;load=app_image.so              ; Image Transmission Application           
;load=app_intercom.so           ; OBSOLETE
;load=app_lookupblacklist.so    ; Look up Caller*ID name/number from black 
;load=app_lookupcidname.so      ; Look up CallerID Name from local databas 
load => app_macro.so              ; Extension Macros                         
load => app_meetme.so             ; MeetMe conference bridge                 
;load=app_milliwatt.so          ; Digital Milliwatt (mu-law) Test Applicat 
;load=app_mp3.so                ; Silly MP3 Application                    
;load=app_nbscat.so             ; Silly NBS Stream Application             
;load=app_parkandannounce.so    ; Call Parking and Announce Application    
load => app_playback.so           ; Trivial Playback Application             
;load=app_privacy.so            ; Require phone number to be entered, if n 
;load=app_qcall.so              ; Call from Queue                          
;load=app_queue.so              ; True Call Queueing                       
;load=app_random.so             ; Random goto                              
load => app_read.so               ; Read Variable Application                
;load=app_record.so             ; Trivial Record Application               
;load=app_sayunixtime.so        ; Say time                                 
;load=app_senddtmf.so           ; Send DTMF digits Application             
;load=app_sendtext.so           ; Send Text Applications                   
load => app_setcallerid.so        ; Set CallerID Application                 
;load=app_setcdruserfield.so    ; CDR user field apps                      
load => app_setcidname.so         ; Set CallerID Name                        
load => app_setcidnum.so          ; Set CallerID Number                      
;load=app_sms.so                ; SMS/PSTN handler                         
;load=app_softhangup.so         ; Hangs up the requested channel           
;load=app_sql_postgres.so       ; Simple PostgreSQL Interface              
;load=app_striplsd.so           ; Strip trailing digits                    
;load=app_substring.so          ; (Deprecated) Save substring digits in a
load => app_system.so             ; Generic System() application             
;load=app_talkdetect.so         ; Playback with Talk Detection             
;load=app_test.so               ; Interface Test Application               
;load=app_transfer.so           ; Transfer                                 
;load=app_txtcidname.so         ; TXTCIDName                               
;load=app_url.so                ; Send URL Applications                    
;load=app_userevent.so          ; Custom User Event Application            
load => app_verbose.so            ; Send verbose output                      
;load=app_voicemail.so          ; Comedian Mail (Voicemail System)         
;load=app_waitforring.so        ; Waits until first ring after time        
;load=app_zapateller.so         ; Block Telemarketers with Special Informa 
;load=app_zapbarge.so           ; Barge in on Zap channel application      
;load=app_zapras.so             ; Zap RAS Application                      
;load=app_zapscan.so            ; Scan Zap channels application            
load => pbx_config.so             ; Text Extension Configuration             
load => pbx_spool.so              ; Outgoing Spool Support                   
;load=pbx_wilcalu.so            ; Wil Cal U (Auto Dialer)                  
load => pbx_functions.so
load => func_uri.so
load => func_logic.so
load => chan_dahdi.so
load => chan_features.so
load => app_readexten.so
load => func_db.so
load => func_channel.so
load => func_strings.so
load => res_agi.so
load => res_rtp_asterisk.so

[global]
;chan_modem.so=yes
<?PHP
	          $message = ob_get_clean();
$fichier = fopen('./config/modules.conf', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }

	    	    	    	    	    	ob_start();
?>;
; indications.conf
;
; Configuration file for location specific tone indications
;
[general]
country=us		; default location

[us]
description = United States / North America
ringcadence = 2000,4000
dial = 350+440
busy = 480+620/500,0/500
ring = 440+480/2000,0/4000
congestion = 480+620/250,0/250
callwaiting = 440/300,0/10000
dialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,0
stutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440

<?PHP
	          $message = ob_get_clean();
$fichier = fopen('./config/indications.conf', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	   	ob_start();
?><icecast>
    <limits>
        <sources><?PHP echo $max_stream_no; ?></sources>
    </limits>
    <authentication>
        <source-password><?PHP echo $master_key; ?></source-password>
        <relay-password><?PHP echo $master_key; ?></relay-password>
        <admin-user>admin</admin-user>
        <admin-password><?PHP echo $master_key; ?></admin-password>
    </authentication>

    <hostname><?PHP echo $server_out; ?></hostname>
    <listen-socket>
        <port><?PHP echo $port; ?></port>
	<!-- new <ssl>1</ssl>-->
    </listen-socket>
    <fileserve>1</fileserve>
    <paths>
       <logdir>/var/log/<?PHP echo $icecast_bin; ?></logdir>
        <webroot>/usr/share/<?PHP echo $icecast_bin; ?>/web</webroot>
        <adminroot>/usr/share/<?PHP echo $icecast_bin; ?>/admin</adminroot>
        <pidfile>/var/run/<?PHP echo $icecast_bin; ?>/icecast.pid</pidfile>
	<alias source="/" dest="/status.xsl"/>
	<!--new<ssl-certificate>/etc/pki/tls/certs/server-dummy</ssl-certificate>-->
   </paths>
    <logging>
        <accesslog>access.log</accesslog>
        <errorlog>error.log</errorlog>
      	<loglevel>2</loglevel> <!-- 4 Debug, 3 Info, 2 Warn, 1 Error -->
    </logging>
    <?PHP
    	   $db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		$cong_name=$data[0];
		$cong_id=$data[1];
		$meetme_admin_pin=$data[2];
		$meetme_user_pin=$data[3];
		$phone_no=$data[4];
		$voip_type=$data[5]; //none/sip/iax
		$stream=$data[6]; //yes/no
		$stream_type=$data[7]; //ogg/mp3/both
		$voip_password=$data[8]; //rand 16char
		$trunk=$data[9]; //yes/no
		$record=$data[10]; //yes/no
		$answer=$data[11]; //yes/no 
		$stream_server=$data[14];
		if ($stream_server=="") $stream_server=$server_in;
		
		if ($stream_type=="ogg"){
			$stream_path="/stream-".$cong_name.".ogg";
			}else{
			$stream_path="/stream-".$cong_name;
			}
			//do we need to record the stream (dump-file)? Only if we use edcast to stream.
$dump_file="";
if($record=='yes' AND $voip_type=='none'){
	if ($stream_type=="ogg"){
	$dump_file='
	<dump-file>'.$web_server_root.'kh-live/records/'.$cong_name.'-%Y%m%d_%H%M%S.ogg</dump-file>
	';
	}else{
	$dump_file='
	<dump-file>'.$web_server_root.'kh-live/records/'.$cong_name.'-%Y%m%d_%H%M%S.mp3</dump-file>
	';
	}
}
if ($stream=='yes'){

		?>
		
<!--mount-<?PHP echo $cong_name; ?>-->
<mount>
	<mount-name><?PHP echo $stream_path; ?></mount-name>
	<username>source</username>
        <password><?PHP echo $voip_password; ?></password><?PHP echo $dump_file; ?>
	
<authentication type="url">
	<option name="mount_add" value="http://<?PHP echo $server_in; ?>/kh-live/stream_start.php"/>
        <option name="mount_remove" value="http://<?PHP echo $server_in; ?>/kh-live/stream_end.php"/>
	<option name="listener_add" value="http://<?PHP echo $server_in; ?>/kh-live/listener_joined.php"/>
        <option name="listener_remove" value="http://<?PHP echo $server_in; ?>/kh-live/listener_left.php"/>
	<option name="auth_header" value="icecast-auth-user: 1"/>
</authentication>
</mount>
<!--mount-end-<?PHP echo $cong_name ; ?>-->
<?PHP
if ($stream_type=='both'){
?>
<!--mount-<?PHP echo $cong_name; ?>-->
<mount>
	<mount-name><?PHP echo "/stream-".$cong_name.".ogg"; ?></mount-name>
	<username>source</username>
        <password><?PHP echo $voip_password; ?></password>
<authentication type="url">
	<option name="mount_add" value="http://<?PHP echo $server_in; ?>/kh-live/stream_start.php"/>
        <option name="mount_remove" value="http://<?PHP echo $server_in; ?>/kh-live/stream_end.php"/>
	<option name="listener_add" value="http://<?PHP echo $server_in; ?>/kh-live/listener_joined.php"/>
        <option name="listener_remove" value="http://<?PHP echo $server_in; ?>/kh-live/listener_left.php"/>
	<option name="auth_header" value="icecast-auth-user: 1"/>
</authentication>
</mount>
<!--mount-end-<?PHP echo $cong_name ; ?>-->
<?PHP
}
}
}
//not sure if we need changeowner on alpine...
    ?>
<!--lastmount-->
<security>
        <chroot>0</chroot>
        <changeowner>
            <user>asterisk</user>
            <group>asterisk</group>
        </changeowner>
    </security>
</icecast>
<?PHP
	 	          $message = ob_get_clean();
$fichier = fopen('./config/icecast.xml', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	    exec("kill -s HUP $(pidof ".$icecast_bin.")");
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
$db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		$cong_name=$data[0];
		$cong_id=$data[1];
		$meetme_admin_pin=$data[2];
		$meetme_user_pin=$data[3];
		$phone_no=$data[4];
		$voip_type=$data[5]; //none/sip/iax
		$stream=$data[6]; //yes/no
		$stream_type=$data[7]; //ogg/mp3
		$voip_password=$data[8]; //rand 16char
		$trunk=$data[9]; //yes/no
		$record=$data[10]; //yes/no
		$answer=$data[11]; //yes/no 
		$stream_quality=$data[12];
		$stream_server=$data[14];
		if ($stream_server=="") $stream_server=$server_in;
		if ($stream_type=="mp3"){
			$stream_path="/stream-".$cong_name;
			}else{
			$stream_path="/stream-".$cong_name.".ogg";
			}
			if ($stream=='yes'){
				if ($stream_type=="mp3" OR $stream_type=="both"){
		$bitrates=array(
		'8000-0' => '8',
		'8000-1' => '16',
		'8000-2' => '24',
		'8000-3' => '32',
		'8000-4' => '40',
		'8000-5' => '48',
		'8000-6' => '56',
		'8000-7' => '64',
		'8000-8' => '64',
		'8000-9' => '64',
		'8000-10' => '64',
		'11025-0' => '8',
		'11025-1' => '16',
		'11025-2' => '24',
		'11025-3' => '32',
		'11025-4' => '40',
		'11025-5' => '48',
		'11025-6' => '56',
		'11025-7' => '64',
		'11025-8' => '64',
		'11025-9' => '64',
		'11025-10' => '64',
		'16000-0' => '16',
		'16000-1' => '24',
		'16000-2' => '32',
		'16000-3' => '40',
		'16000-4' => '48',
		'16000-5' => '56',
		'16000-6' => '64',
		'16000-7' => '80',
		'16000-8' => '96',
		'16000-9' => '112',
		'16000-10' => '128',
		'22050-0' => '24',
		'22050-1' => '32',
		'22050-2' => '40',
		'22050-3' => '48',
		'22050-4' => '56',
		'22050-5' => '64',
		'22050-6' => '80',
		'22050-7' => '96',
		'22050-8' => '112',
		'22050-9' => '128',
		'22050-10' => '144',
		'32000-0' => '32',
		'32000-1' => '40',
		'32000-2' => '48',
		'32000-3' => '56',
		'32000-4' => '64',
		'32000-5' => '80',
		'32000-6' => '96',
		'32000-7' => '112',
		'32000-8' => '128',
		'32000-9' => '160',
		'32000-10' => '192',
		'44100-0' => '32',
		'44100-1' => '40',
		'44100-2' => '48',
		'44100-3' => '56',
		'44100-4' => '64',
		'44100-5' => '80',
		'44100-6' => '96',
		'44100-7' => '112',
		'44100-8' => '128',
		'44100-9' => '160',
		'44100-10' => '192',
		'48000-0' => '32',
		'48000-1' => '40',
		'48000-2' => '48',
		'48000-3' => '56',
		'48000-4' => '64',
		'48000-5' => '80',
		'48000-6' => '96',
		'48000-7' => '112',
		'48000-8' => '128',
		'48000-9' => '160',
		'48000-10' => '192'
		);
		$bitrate= $bitrates[$sound_quality.'-'.$stream_quality];
$info4 = "<ezstream>
    <url>http://".$stream_server.":".$port."/stream-".$cong_name."</url>
    <sourcepassword>".$voip_password."</sourcepassword>
    <format>MP3</format>
    <filename>stdin</filename>
    <stream_once>1</stream_once>
    <svrinfoname>My Stream</svrinfoname>
    <svrinfourl>http://".$server_out.":".$port."/stream-".$cong_name."</svrinfourl>
    <svrinfogenre>Live calls</svrinfogenre>
    <svrinfodescription>Stream from ".str_replace("_"," ", $cong_name)." Meeting</svrinfodescription>
    <svrinfobitrate>".$bitrate."</svrinfobitrate>
    <svrinfoquality>1</svrinfoquality>
    <svrinfochannels>1</svrinfochannels>
    <svrinfosamplerate>".$sound_quality."</svrinfosamplerate>
    <svrinfopublic>0</svrinfopublic>
</ezstream>";
$file=fopen('./config/asterisk-ezstream-'.$cong_name.'.xml','w');
			if(fputs($file,$info4)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'4</div>';
			}
$info5="#!/bin/sh\ncat /dev/fd/3 | ".$lame_bin." --preset cbr ".$bitrate." -r -m m -s 8.0 --bitwidth 16 - - | ".$ezstream_bin." -c ".$web_server_root."/kh-live/config/asterisk-ezstream-".$cong_name.".xml";
$file=fopen('./config/mp3stream-'.$cong_name.'.sh','w');
			if(fputs($file,$info5)){
			fclose($file);
			//the file needs to have exec rights to work as an agi script we might not need to give 5 to nobody
			chmod('./config/mp3stream-'.$cong_name.'.sh', 0755);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'5</div>';
			}
}
if($stream_type=="ogg" OR $stream_type=="both"){			
$info4="<?xml version=\"1.0\"?>
<ices>
    <background>0</background>
    <logpath>/var/log/icecast2</logpath>
    <logfile>ices.log</logfile>
    <loglevel>2</loglevel>
    <consolelog>0</consolelog>
    <stream>
        <metadata>
            <name>".str_replace("_"," ", $cong_name)." Meeting </name>
            <genre>Live calls</genre>
            <description>Stream from ".str_replace("_"," ", $cong_name)." Meeting</description>
            <url>http://".$server_out.":".$port."/stream-".$cong_name.".ogg</url>
        </metadata>
        <input>
            <module>stdinpcm</module>
            <param name=\"rate\">".$sound_quality."</param>
            <param name=\"channels\">1</param>
            <param name=\"metadata\">0</param>
            <param name=\"metadatafilename\"> </param>
        </input>
        <instance>
            <hostname>".$stream_server."</hostname>
            <port>".$port."</port>
            <password>".$voip_password."</password>
            <mount>/stream-".$cong_name.".ogg</mount>
            <yp>0</yp>
            <encode>  
                <quality>".$stream_quality."</quality>
                <samplerate>".$sound_quality."</samplerate>
                <channels>1</channels>
            </encode>
            <downmix>0</downmix>
        </instance>
    </stream>
</ices>
";
			$file=fopen('./config/asterisk-ices-'.$cong_name.'.xml','w');
			if(fputs($file,$info4)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'4</div>';
			}
		}

ob_start();
?>Channel: Local/ices_<?PHP echo $cong_name; ?>@test-menu
MaxRetries: 0
WaitTime: 60
Context: test-menu
Extension: meet_me_<?PHP echo $cong_name; ?> 
Priority: 1
<?PHP
	 	          $message = ob_get_clean();
$fichier = fopen('./config/stream_'.$cong_name.'.call', 'w');
            if (fwrite($fichier, $message)){
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
	    
	  }
	}
	
include "sip-gen.php";
include "alsa-gen.php";
include "iax-gen.php";

	}
}
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){

ob_start();
       
        echo '<?PHP
	/**last change on : '.date("F d Y H:i:s").'**/
$version=\''.$gen_version.'\';
$max_song_no=\''.$max_song_no.'\';
' ;
	foreach ($_POST as $key=>$value){
if ($key!="submit"){
	if (get_magic_quotes_gpc()) {
echo '$'.$key.'=\''.$value."';\n";
}else{
echo '$'.$key.'=\''.addslashes($value)."';\n";
}
	
	}
}
echo '?>';
    $message = ob_get_clean();
$fichier = fopen('./db/config.php', 'w');
            if (fwrite($fichier, $message)){
            echo "<div id=\"page\"><br /><b style=\"color:green;\">Configuration saved successfully! </b>Don't forget to apply the changes.</div></body></html>" ;
            
            fclose ($fichier);
	    $tmp_skip='yes'; //this is to give time to the system to write the config so the fresh one can be loaded
	    }else{
	    // error saving
	    }
	}
}
if ($tmp_skip=='no'){
//db/streams is not regenerated...
include "db/config.php";
?>
<script>
function redoconfig(){
var r=confirm("Are you sure you want to overwrite all the config files ?");
if (r==true)
  {
  window.location="./configure?action=ok" ;
  }
else
  {
  window.location="./configure";
  }
}
</script>
<div id="page">
<h2>Configuration</h2>
<form action="./configure" method="post">
<div class="subgroup" onclick="javascript:toogleDiv(1)">General</div>
<div class="subgroups" id="subgroup1">
Server state :<br />enable testing functions - the meeting is faked live, all errors are displayed,the listening page doesnt refresh<br />
<select class="field_login" name="server_beta" >
<option value="false" <?PHP if ($server_beta=="false") echo 'selected=selected';?>>Production (voip enabled)</option>
<option value="stream" <?PHP if ($server_beta=="stream") echo 'selected=selected';?>>Production (no voip)</option>
<option value="true" <?PHP if ($server_beta=="true") echo 'selected=selected';?>>Testing</option>
<option value="master" <?PHP if ($server_beta=="master") echo 'selected=selected';?>>Master (only use on kh-live.co.za)</option>
</select><br />
server user and group : <br />Set the username and group used to run the servers ( "asterisk:asterisk" by default)<br />
<input class="field_login" type="text" name="server_user_group" value="<?PHP if (isset($server_user_group)) {
echo $server_user_group;
}else{
echo 'asterisk:asterisk';
}?>" /><br />
Server_in : <br />default stream server address. Usually "localhost". Used for : cron and asterisk web server wgets + icecast actions urls + ices instance* + ezstream instance*. *It can be overriden by congregation config.<br />
<input class="field_login" type="text" name="server_in" value="<?PHP echo $server_in;?>" /><br />
server_out : <br />Fully qualified Server name (must be the same as set on kh-live.co.za). This is the address at which the server is reachable from the internet.<br />
<input class="field_login" type="text" name="server_out" value="<?PHP echo $server_out;?>" /><br />
timer : <br />used to reload  meeting page<br />
<input class="field_login" type="text" name="timer" value="<?PHP echo $timer;?>" /><br />
timer_listen :<br />listening timer <br />
<input class="field_login" type="text" name="timer_listen" value="<?PHP echo $timer_listen;?>" /><br />
Auto config ppp0 :<br />keep alive ppp0 by sending ifup every 5min<br />
<select class="field_login" name="auto_ppp" >
<option value="no">No</option>
<option value="yes" <?PHP if ($auto_ppp=="yes") echo 'selected=selected';?>>Yes</option>
</select><br />
Auto config governor :<br />auto change governor to performance every 5min<br />
<select class="field_login" name="auto_gov" >
<option value="yes">Yes</option>
<option value="no" <?PHP if ($auto_gov=="no") echo 'selected=selected';?>>No</option>
</select><br />
Auto config cron :<br />update khlive cron file every 5min<br />
<select class="field_login" name="auto_cron" >
<option value="yes">Yes</option>
<option value="no" <?PHP if ($auto_cron=="no") echo 'selected=selected';?>>No</option>
</select><br />
Enable meeting scheduler<br />yes -> the link for scheduler will be shown in menu <br />no -> the scheduler is disabled <br />
<select class="field_login" name="scheduler" >
<option value="no">no</option>
<option value="yes" <?PHP if (@$scheduler=="yes") echo 'selected=selected';?>>yes</option>
</select><br />
Enable video downloader<br />yes -> the link for videos will be shown in menu (and videos are downloaded at 00:05) <br />no -> the video downloader is disabled <br />
<select class="field_login" name="video_dowloader" >
<option value="no">no</option>
<option value="yes" <?PHP if (@$video_dowloader=="yes") echo 'selected=selected';?>>yes</option>
</select><br />
Enable failsafe automatic meeting stop<br />yes -> any meeting still streaming at 23:55 will be stopped automatically <br />no ->automatic failsafe is disabled <br />
<select class="field_login" name="auto_stop" >
<option value="no">no</option>
<option value="yes" <?PHP if (@$auto_stop=="yes") echo 'selected=selected';?>>yes</option>
</select><br />
</div>

<div class="subgroup" onclick="javascript:toogleDiv(2)">FreeDNS</div>
<div class="subgroups" id="subgroup2">
Auto config freedns :<br />update ip address at freedns every 5min<br />
<select class="field_login" name="auto_dns" >
<option value="yes">Yes</option>
<option value="no" <?PHP if ($auto_dns=="no") echo 'selected=selected';?>>No</option>
</select><br />
mooo.com address : <br />
<input class="field_login" type="text" name="moo_adr" value="<?PHP echo @$moo_adr;?>" /><br />
moo_key : <br />api key for link up with mooo.com server<br />
<input class="field_login" type="text" name="moo_key" value="<?PHP echo @$moo_key;?>" /><br />
</div>

<div class="subgroup" onclick="javascript:toogleDiv(3)">khlive.co.za</div>
<div class="subgroups" id="subgroup3">
Auto config kh-live.co.za :<br />update ip address at kh-live.co.za every 5min<br />and linkup users changes with kh-live.co.za<br />
<select class="field_login" name="auto_khlive" >
<option value="yes">Yes</option>
<option value="no" <?PHP if ($auto_khlive=="no") echo 'selected=selected';?>>No</option>
</select><br />
api_key : <br />api key for link up with main server<br />
<input class="field_login" type="text" name="api_key" value="<?PHP echo @$api_key;?>" /><br />
master_key : <br />key for ip synch with main server and pwd for icecast admin<br />
<input class="field_login" type="text" name="master_key" value="<?PHP echo @$master_key;?>" /><br />
</div>

<div class="subgroup" onclick="javascript:toogleDiv(4)">Diagnosis</div>
<div class="subgroups" id="subgroup4">
test_url : <br />test url ex kh.sinux.ch check if nslookup works<br />
<input class="field_login" type="text" name="test_url" value="<?PHP echo $test_url;?>" /><br />
test_ip :<br />local ip to ping<br />
<input class="field_login" type="text" name="test_ip" value="<?PHP echo $test_ip;?>" /><br />
</div>

<div class="subgroup" onclick="javascript:toogleDiv(5)">Audio interface</div>
<div class="subgroups" id="subgroup5">
<?PHP
if (!isset($max_stream_no)){
$max_stream_no="2";
}
?>
Maximum number of simutaneous streams (note that one congregation can use two streams at once)<br />
<select class="field_login" name="max_stream_no" >
<option value="2">2</option>
<option value="3" <?PHP if ($max_stream_no=="3") echo 'selected=selected';?>>3</option>
<option value="4" <?PHP if ($max_stream_no=="4") echo 'selected=selected';?>>4</option>
<option value="5" <?PHP if ($max_stream_no=="5") echo 'selected=selected';?>>5</option>
<option value="6" <?PHP if ($max_stream_no=="6") echo 'selected=selected';?>>6</option>
<option value="7" <?PHP if ($max_stream_no=="7") echo 'selected=selected';?>>7</option>
<option value="8" <?PHP if ($max_stream_no=="8") echo 'selected=selected';?>>8</option>
<option value="9" <?PHP if ($max_stream_no=="9") echo 'selected=selected';?>>9</option>
<option value="10" <?PHP if ($max_stream_no=="10") echo 'selected=selected';?>>10</option>
</select><br />
<?PHP
if (!isset($sound_quality)){
$sound_quality="8000";
}
?>
Stream and Recording sample rate in Hz. 8000Hz is the default on raspberry pi. <br />
The higher the better quality, but it will also consume more processing power and more storage and more bandwidth.<br />
(In brackets the highest frequency recorded is noted - everything above 20kHz can't be heard by humans)<br />
<select class="field_login" name="sound_quality" >
<option value="8000">8000 Hz (3.6kHz - default)</option>
<option value="11025" <?PHP if ($sound_quality=="11025") echo 'selected=selected';?>>11025 Hz (5kHz)</option>
<option value="16000" <?PHP if ($sound_quality=="16000") echo 'selected=selected';?>>16000 Hz (7.2kHz)</option>
<option value="22050" <?PHP if ($sound_quality=="22050") echo 'selected=selected';?>>22050 Hz (10kHz)</option>
<option value="32000" <?PHP if ($sound_quality=="32000") echo 'selected=selected';?>>32000 Hz (14.5kHz)</option>
<option value="44100" <?PHP if ($sound_quality=="44100") echo 'selected=selected';?>>44100 Hz (20kHz - cd quality)</option>
<option value="48000" <?PHP if ($sound_quality=="48000") echo 'selected=selected';?>>48000 Hz (21.8kHz - default mp3)</option>
<option value="96000" disabled="disabled" >96000 Hz (43.6kHz - Hi Res Audio)</option>
</select><br />
Mp3 encoder speed<br />no of seconds encoded in one second (26 for raspberry B+)<br />
<input class="field_login" type="text" name="encoder_speed" value="<?PHP echo @$encoder_speed;?>" /><br />
<?PHP
if (!isset($song_dev)){
$song_dev="jwapp";
}
?>
Where to play the songs: <br />client -> streams the song to the computer you use to manage the meeting.<br />server -> uses server sound card. <br />vmix -> plays the song on vmix<br />jwapp -> plays the song with jw library app (this should be the default)<br />
<select class="field_login" name="song_dev" >
<option value="client">client</option>
<option value="server" <?PHP if ($song_dev=="server") echo 'selected=selected';?>>server</option>
<option value="vmix" <?PHP if ($song_dev=="vmix") echo 'selected=selected';?>>vmix</option>
<option value="jwapp" <?PHP if ($song_dev=="jwapp") echo 'selected=selected';?>>jwapp</option>
</select><br />
Song type :<br />select which type of song to use<br />
<select class="field_login" name="song_type" >
<option value="normal">Orchestral (until 31.12.2016)</option>
<option value="joy" <?PHP if ($song_type=="joy") echo 'selected=selected';?>>Sing Joyfully (from 01.01.2017)</option>
<?PHP
if ($song_dev=='vmix'){
?>
<option value="vid" <?PHP if ($song_type=="vid") echo 'selected=selected';?>>Music Video with lyrics (from 01.01.2017)</option>
<?PHP
}
?>
</select><br />
Video Song quality :<br />select which quality of video songs to use<br />
<select class="field_login" name="song_quality" >
<option value="..." >select the quality...</option>
<option value="240" <?PHP if (@$song_quality=="240") echo 'selected=selected';?>>240P not recommanded</option>
<option value="360" <?PHP if (@$song_quality=="360") echo 'selected=selected';?>>360P not recommanded</option>
<option value="480" <?PHP if (@$song_quality=="480") echo 'selected=selected';?>>480P good enough</option>
<option value="720" <?PHP if (@$song_quality=="720") echo 'selected=selected';?>>720P best</option>
</select><br />
Asterisk Audio device :<br />select which input device to use on direct input<br />
<select class="field_login" name="server_audio" >
<option value="0">None</option>
<option value="alsa" <?PHP if ($server_audio=="alsa") echo 'selected=selected';?>>Alsa</option>
<option value="dsp" <?PHP if ($server_audio=="dsp") echo 'selected=selected';?>>Oss (/dev/dsp)</option>
</select><br />
Asterisk Direct input hw :<br />hardware for input (default)<br />
<input class="field_login" type="text" name="alsa_in" value="<?PHP echo @$alsa_in;?>" /><br />
Asterisk Direct output hw :<br />hardware for output (default)<br />
<input class="field_login" type="text" name="alsa_out" value="<?PHP echo @$alsa_out;?>" /><br />
</div>
<div class="subgroup" onclick="javascript:toogleDiv(6)">vMix</div>
<div class="subgroups" id="subgroup6">
Enable vmix integration<br />yes -> the vmix control panel will be shown on meeting page <br />no -> vmix is disabled <br />
<select class="field_login" name="vmix" >
<option value="no">no</option>
<option value="yes" <?PHP if (@$vmix=="yes") echo 'selected=selected';?>>yes</option>
</select><br />
vmix server address : <br />this should be the local computer where vmix is running's address (see in vmix->settings->web controller). this is the ip:port . f eg : 192.168.1.18:8088<br />
<input class="field_login" type="text" name="vmix_url" value="<?PHP echo @$vmix_url;?>" /><br />
year text path : <br />where is the year text stored on local computer with trailing \ . the year text filename must be : YT-201x-cong_name.jpg. f eg c:\users\admin\documents\<br />
<input class="field_login" type="text" name="vmix_path" value="<?PHP echo @$vmix_path;?>" /><br />
music video path : <br />where is the music video with lyrics stored on local computer with trailing \ . f eg c:\users\admin\documents\<br />
<input class="field_login" type="text" name="vmix_song_path" value="<?PHP echo @$vmix_song_path;?>" /><br />
library path : <br />where is the library files are stored on local computer with trailing \ . f eg c:\users\admin\documents\<br />
<input class="field_login" type="text" name="vmix_lib_path" value="<?PHP echo @$vmix_lib_path;?>" /><br />
Enable vmix autopause<br />yes -> vmix pauses automatically an input that's not active anymore <br />no -> vmix auto pause is disabled <br />
<select class="field_login" name="vmix_auto_pause" >
<option value="no">no</option>
<option value="yes" <?PHP if (@$vmix_auto_pause=="yes") echo 'selected=selected';?>>yes</option>
</select><br />
</div>
<div class="subgroup" onclick="javascript:toogleDiv(7)">Timing</div>
<div class="subgroups" id="subgroup7">
Enable meeting timing<br />yes -> the link for timing will be shown in menu <br />no -> timing is disabled <br />
<select class="field_login" name="timing_conf" >
<option value="no">no</option>
<option value="yes" <?PHP if (@$timing_conf=="yes") echo 'selected=selected';?>>yes</option>
</select><br />
Timing standalone style<br />default -> use the default timing style<br />testing -> use the testing timing style<br />
<select class="field_login" name="timing_style" >
<option value="default">default</option>
<option value="testing" <?PHP if (@$timing_syle=="testing") echo 'selected=selected';?>>testing</option>
</select><br />
Timing standalone font size for small text<br />
<select class="field_login" name="timing_font_size_1" >
<option value="1">1 (default)</option>
<option value="2" <?PHP if (@$timing_font_size_1=="2") echo 'selected=selected';?>>2</option>
<option value="3" <?PHP if (@$timing_font_size_1=="3") echo 'selected=selected';?>>3</option>
<option value="4" <?PHP if (@$timing_font_size_1=="4") echo 'selected=selected';?>>4</option>
<option value="5" <?PHP if (@$timing_font_size_1=="5") echo 'selected=selected';?>>5</option>
<option value="6" <?PHP if (@$timing_font_size_1=="6") echo 'selected=selected';?>>6</option>
<option value="7" <?PHP if (@$timing_font_size_1=="7") echo 'selected=selected';?>>7</option>
<option value="8" <?PHP if (@$timing_font_size_1=="8") echo 'selected=selected';?>>8</option>
<option value="9" <?PHP if (@$timing_font_size_1=="9") echo 'selected=selected';?>>9</option>
<option value="10" <?PHP if (@$timing_font_size_1=="10") echo 'selected=selected';?>>10</option>
</select><br />
<?PHP
if (!isset($timing_font_size_2)){
$timing_font_size_2="3";
}
?>
Timing standalone font size for clock<br />
<select class="field_login" name="timing_font_size_2" >
<option value="1" <?PHP if (@$timing_font_size_2=="1") echo 'selected=selected';?>>1</option>
<option value="2" <?PHP if (@$timing_font_size_2=="2") echo 'selected=selected';?>>2</option>
<option value="3" <?PHP if (@$timing_font_size_2=="3") echo 'selected=selected';?>>3 (default)</option>
<option value="4" <?PHP if (@$timing_font_size_2=="4") echo 'selected=selected';?>>4</option>
<option value="5" <?PHP if (@$timing_font_size_2=="5") echo 'selected=selected';?>>5</option>
<option value="6" <?PHP if (@$timing_font_size_2=="6") echo 'selected=selected';?>>6</option>
<option value="7" <?PHP if (@$timing_font_size_2=="7") echo 'selected=selected';?>>7</option>
<option value="8" <?PHP if (@$timing_font_size_2=="8") echo 'selected=selected';?>>8</option>
<option value="9" <?PHP if (@$timing_font_size_2=="9") echo 'selected=selected';?>>9</option>
<option value="10" <?PHP if (@$timing_font_size_2=="10") echo 'selected=selected';?>>10</option>
<option value="11" <?PHP if (@$timing_font_size_2=="11") echo 'selected=selected';?>>11</option>
<option value="12" <?PHP if (@$timing_font_size_2=="12") echo 'selected=selected';?>>12</option>
<option value="13" <?PHP if (@$timing_font_size_2=="13") echo 'selected=selected';?>>13</option>
<option value="14" <?PHP if (@$timing_font_size_2=="14") echo 'selected=selected';?>>14</option>
<option value="15" <?PHP if (@$timing_font_size_2=="15") echo 'selected=selected';?>>15</option>
<option value="16" <?PHP if (@$timing_font_size_2=="16") echo 'selected=selected';?>>16</option>
<option value="17" <?PHP if (@$timing_font_size_2=="17") echo 'selected=selected';?>>17</option>
<option value="18" <?PHP if (@$timing_font_size_2=="18") echo 'selected=selected';?>>18</option>
<option value="19" <?PHP if (@$timing_font_size_2=="19") echo 'selected=selected';?>>19</option>
<option value="20" <?PHP if (@$timing_font_size_2=="20") echo 'selected=selected';?>>20</option>
</select><br />
Meeting timing vmix overlay multiplier<br />1 -> normal<br />anything else -> to fit your screen <br />
<select class="field_login" name="timing_multi" >
<option value="1">1</option>
<option value="0.75" <?PHP if (@$timing_multi=="0.75") echo 'selected=selected';?>>0.75</option>
<option value="0.5" <?PHP if (@$timing_multi=="0.5") echo 'selected=selected';?>>0.5</option>
</select><br />
</div>
<div class="subgroup" onclick="javascript:toogleDiv(8)">Security</div>
<div class="subgroups" id="subgroup8">
Enable developper's root account ? <br /> This account is used by the developper to login on your server and help you solve problems.<br />
<select class="field_login" name="devel_account" >
<option value="yes">yes</option>
<option value="no" <?PHP if (@$devel_account=="no") echo 'selected=selected';?>>no</option>
</select><br />
<?PHP
if (!isset($qpin_max)){
$qpin_max="3";
}
?>
Quick login pin maximum failed attempts before lock out<br />
<select class="field_login" name="qpin_max" >
<option value="1" <?PHP if (@$qpin_max=="1") echo 'selected=selected';?>>1</option>
<option value="2" <?PHP if (@$qpin_max=="2") echo 'selected=selected';?>>2</option>
<option value="3" <?PHP if (@$qpin_max=="3") echo 'selected=selected';?>>3 (default)</option>
<option value="4" <?PHP if (@$qpin_max=="4") echo 'selected=selected';?>>4</option>
<option value="5" <?PHP if (@$qpin_max=="5") echo 'selected=selected';?>>5</option>
<option value="6" <?PHP if (@$qpin_max=="6") echo 'selected=selected';?>>6</option>
<option value="7" <?PHP if (@$qpin_max=="7") echo 'selected=selected';?>>7</option>
<option value="8" <?PHP if (@$qpin_max=="8") echo 'selected=selected';?>>8</option>
<option value="9" <?PHP if (@$qpin_max=="9") echo 'selected=selected';?>>9</option>
<option value="10" <?PHP if (@$qpin_max=="10") echo 'selected=selected';?>>10</option>
</select><br />
Quick login lock out time. <br />(How long must the user wait before being able to login again)<br />
<select class="field_login" name="qpin_time" >
<option value="1" <?PHP if (@$qpin_time=="1") echo 'selected=selected';?>>1 min (default)</option>
<option value="2" <?PHP if (@$qpin_time=="2") echo 'selected=selected';?>>2 min</option>
<option value="3" <?PHP if (@$qpin_time=="3") echo 'selected=selected';?>>3 min</option>
<option value="4" <?PHP if (@$qpin_time=="4") echo 'selected=selected';?>>4 min</option>
<option value="5" <?PHP if (@$qpin_time=="5") echo 'selected=selected';?>>5 min</option>
<option value="6" <?PHP if (@$qpin_time=="6") echo 'selected=selected';?>>6 min</option>
<option value="7" <?PHP if (@$qpin_time=="7") echo 'selected=selected';?>>7 min</option>
<option value="8" <?PHP if (@$qpin_time=="8") echo 'selected=selected';?>>8 min</option>
<option value="9" <?PHP if (@$qpin_time=="9") echo 'selected=selected';?>>9 min</option>
<option value="10" <?PHP if (@$qpin_time=="10") echo 'selected=selected';?>>10 min</option>
</select><br />
</div>
<div class="subgroup" onclick="javascript:toogleDiv(9)">Paths and Binaries</div>
<div class="subgroups" id="subgroup9">
web_server_root : <br />root for webserver with trailing /<br />
<input class="field_login" type="text" name="web_server_root" value="<?PHP echo $web_server_root;?>" /><br />
temp_dir : <br />temp directory /dev/shm with trailing / <br />
<input class="field_login" type="text" name="temp_dir" value="<?PHP echo $temp_dir;?>" /><br />
asterisk_bin : <br />asterisk binary path + file_name<br />
<input class="field_login" type="text" name="asterisk_bin" value="<?PHP echo $asterisk_bin;?>" /><br />
asterisk_spool : <br />asterisk spool folder with trailing /<br />
<input class="field_login" type="text" name="asterisk_spool" value="<?PHP echo $asterisk_spool;?>" /><br />
lame_bin :<br />lame binary path + file_name<br />
<input class="field_login" type="text" name="lame_bin" value="<?PHP echo $lame_bin;?>" /><br />
ezstream_bin : <br />ezstream binary path + file_name<br />
<input class="field_login" type="text" name="ezstream_bin" value="<?PHP echo $ezstream_bin;?>" /><br />
ices_bin : <br />ices binary path + file_name<br />
<?PHP
if (!isset($ices_bin)){
$ices_bin="/usr/bin/ices2";
}
?>
<input class="field_login" type="text" name="ices_bin" value="<?PHP echo $ices_bin;?>" /><br />
icecast_bin : <br />icecast binary name (icecast on alpine icecast2 on debian)<br />
<input class="field_login" type="text" name="icecast_bin" value="<?PHP echo $icecast_bin;?>" /><br />
port :<br />icecast port <br />
<input class="field_login" type="text" name="port" value="<?PHP echo $port;?>" /><br />
</div>

<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
<hr />
Use this button to re-generate and apply all the configuration files changes from db. Don't forget to save first!<br />
<input type="submit" value="Apply configuration changes" onclick="javascript:redoconfig()" />
</div>
<script type="text/javascript">
function toogleDiv(id){
if (document.getElementById("subgroup" + id).style.display=="block"){
document.getElementById("subgroup" + id).style.display="";
}else{
document.getElementById("subgroup" + id).style.display="block";
}
}
</script>
<?PHP
}
?>
