<?PHP
$gen_version='1.6';
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
<?PHP if ($auto_ppp=="yes") echo 'ifup ppp0 1>&- 2>&-'; ?>

<?PHP if ($auto_cron=="yes") echo 'cp -u "/var/www/kh-live/config/cron" "/etc/cron.d/khlive"'; ?>

<?PHP if ($auto_gov=="yes") echo 'echo performance > /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor'; ?>

<?PHP if ($auto_dns=="yes") {
?>
UPDATEURL="http://freedns.afraid.org/dynamic/update.php?<?PHP echo $moo_key ; ?>"
DOMAIN="<?PHP echo $moo_adr ; ?>"

registered=$(nslookup $DOMAIN|tail -n2|grep A|sed s/[^0-9.]//g)

  current=$(wget -q -O - http://checkip.dyndns.org|sed s/[^0-9.]//g)
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
?>*/5 * * * * root /var/www/kh-live/config/update.sh
5 0 * * * root /var/www/kh-live/config/downloader.sh
<?PHP
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

[at]
description = Austria
ringcadence = 1000,5000
dial = 420
busy = 420/400,0/400
ring = 420/1000,0/5000
congestion = 420/200,0/200
callwaiting = 420/40,0/1960
dialrecall = 420
record = 1400/80,0/14920
info = 950/330,1450/330,1850/330,0/1000
stutter = 380+420

[au]
description = Australia
ringcadence = 400,200,400,2000
dial = 413+438
busy = 425/375,0/375
ring = 413+438/400,0/200,413+438/400,0/2000
congestion = 425/375,0/375,420/375,0/375
callwaiting = 425/200,0/200,425/200,0/4400
dialrecall = 413+438
record = !425/1000,!0/15000,425/360,0/15000
info = 425/2500,0/500
std = !525/100,!0/100,!525/100,!0/100,!525/100,!0/100,!525/100,!0/100,!525/100
facility = 425
stutter = 413+438/100,0/40
ringmobile = 400+450/400,0/200,400+450/400,0/2000

[bg]
description = Bulgaria
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/250,0/250
callwaiting = 425/150,0/150,425/150,0/4000
dialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425
record = 1400/425,0/15000
info = 950/330,1400/330,1800/330,0/1000
stutter = 425/1500,0/100

[br]
description = Brazil
ringcadence = 1000,4000
dial = 425
busy = 425/250,0/250
ring = 425/1000,0/4000
congestion = 425/250,0/250,425/750,0/250
callwaiting = 425/50,0/1000
dialrecall = 350+440
record = 425/250,0/250
info = 950/330,1400/330,1800/330
stutter = 350+440

[be]
description = Belgium
ringcadence = 1000,3000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/3000
congestion = 425/167,0/167
callwaiting = 1400/175,0/175,1400/175,0/3500
dialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440
record = 1400/500,0/15000
info = 900/330,1400/330,1800/330,0/1000
stutter = 425/1000,0/250

[ch]
description = Switzerland
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 425/200,0/200,425/200,0/4000
dialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425
record = 1400/80,0/15000
info = 950/330,1400/330,1800/330,0/1000
stutter = 425+340/1100,0/1100

[cl]
description = Chile
ringcadence = 1000,3000
dial = 400
busy = 400/500,0/500
ring = 400/1000,0/3000
congestion = 400/200,0/200
callwaiting = 400/250,0/8750
dialrecall = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400
record = 1400/500,0/15000
info = 950/333,1400/333,1800/333,0/1000
stutter = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400

[cn]
description = China
ringcadence = 1000,4000
dial = 450
busy = 450/350,0/350
ring = 450/1000,0/4000
congestion = 450/700,0/700
callwaiting = 450/400,0/4000
dialrecall = 450
record = 950/400,0/10000
info = 450/100,0/100,450/100,0/100,450/100,0/100,450/400,0/400
stutter = 450+425

[cz]
description = Czech Republic
ringcadence = 1000,4000
dial = 425/330,0/330,425/660,0/660
busy = 425/330,0/330
ring = 425/1000,0/4000
congestion = 425/165,0/165
callwaiting = 425/330,0/9000
dialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425/330,0/330,425/660,0/660
record = 1400/500,0/14000
info = 950/330,0/30,1400/330,0/30,1800/330,0/1000
stutter = 425/450,0/50

[de]
description = Germany
ringcadence = 1000,4000
dial = 425
busy = 425/480,0/480
ring = 425/1000,0/4000
congestion = 425/240,0/240
callwaiting = !425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,0
dialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425
record = 1400/80,0/15000
info = 950/330,1400/330,1800/330,0/1000
stutter = 425+400

[dk]
description = Denmark
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = !425/200,!0/600,!425/200,!0/3000,!425/200,!0/200,!425/200,0
dialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425
record = 1400/80,0/15000
info = 950/330,1400/330,1800/330,0/1000
stutter = 425/450,0/50

[ee]
description = Estonia
ringcadence = 1000,4000
dial = 425
busy = 425/300,0/300
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 950/650,0/325,950/325,0/30,1400/1300,0/2600
dialrecall = 425/650,0/25
record = 1400/500,0/15000
info = 950/650,0/325,950/325,0/30,1400/1300,0/2600
stutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425

[es]
description = Spain
ringcadence = 1500,3000
dial = 425
busy = 425/200,0/200
ring = 425/1500,0/3000
congestion = 425/200,0/200,425/200,0/200,425/200,0/600
callwaiting = 425/175,0/175,425/175,0/3500
dialrecall = !425/200,!0/200,!425/200,!0/200,!425/200,!0/200,425
record = 1400/500,0/15000
info = 950/330,0/1000
dialout = 500

[fi]
description = Finland
ringcadence = 1000,4000
dial = 425
busy = 425/300,0/300
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 425/150,0/150,425/150,0/8000
dialrecall = 425/650,0/25
record = 1400/500,0/15000
info = 950/650,0/325,950/325,0/30,1400/1300,0/2600
stutter = 425/650,0/25

[fr]
description = France
ringcadence = 1500,3500
dial = 440
busy = 440/500,0/500
ring = 440/1500,0/3500
congestion = 440/250,0/250
callwait = 440/300,0/10000
dialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330
stutter = !440/100,!0/100,!440/100,!0/100,!440/100,!0/100,!440/100,!0/100,!440/100,!0/100,!440/100,!0/100,440

[gr]
description = Greece
ringcadence = 1000,4000
dial = 425/200,0/300,425/700,0/800
busy = 425/300,0/300
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 425/150,0/150,425/150,0/8000
dialrecall = 425/650,0/25
record = 1400/400,0/15000
info = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0
stutter = 425/650,0/25

[hu]
description = Hungary
ringcadence = 1250,3750
dial = 425
busy = 425/300,0/300
ring = 425/1250,0/3750
congestion = 425/300,0/300
callwaiting = 425/40,0/1960
dialrecall = 425+450
record = 1400/400,0/15000
info = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0
stutter = 350+375+400

[il]
description = Israel
ringcadence = 1000,3000
dial = 414
busy = 414/500,0/500
ring = 414/1000,0/3000
congestion = 414/250,0/250
callwaiting = 414/100,0/100,414/100,0/100,414/600,0/3000
dialrecall = !414/100,!0/100,!414/100,!0/100,!414/100,!0/100,414
record = 1400/500,0/15000
info = 1000/330,1400/330,1800/330,0/1000
stutter = !414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,414

[in]
description = India
ringcadence = 400,200,400,2000
dial = 400*25
busy = 400/750,0/750
ring = 400*25/400,0/200,400*25/400,0/2000
congestion = 400/250,0/250
callwaiting = 400/200,0/100,400/200,0/7500
dialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,0/1000
stutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440

[it]
description = Italy
ringcadence = 1000,4000
dial = 425/200,0/200,425/600,0/1000
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 425/400,0/100,425/250,0/100,425/150,0/14000
dialrecall = 470/400,425/400
record = 1400/400,0/15000
info = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0
stutter = 470/400,425/400

[lt]
description = Lithuania
ringcadence = 1000,4000
dial = 425
busy = 425/350,0/350
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 425/150,0/150,425/150,0/4000
dialrecall = 425/500,0/50
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0
stutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425

[jp]
description = Japan
ringcadence = 1000,2000
dial = 400
busy = 400/500,0/500
ring = 400+15/1000,0/2000
congestion = 400/500,0/500
callwaiting = 400+16/500,0/8000
dialrecall = !400/200,!0/200,!400/200,!0/200,!400/200,!0/200,400
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,0
stutter = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400

[mx]
description = Mexico
ringcadence = 2000,4000
dial = 425
busy = 425/250,0/250
ring = 425/1000,0/4000
congestion = 425/250,0/250
callwaiting = 425/200,0/600,425/200,0/10000
dialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440
record = 1400/500,0/15000
info = 950/330,0/30,1400/330,0/30,1800/330,0/1000
stutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440

[my]
description = Malaysia
ringcadence = 2000,4000
dial = 425
busy = 425/500,0/500
ring = 425/400,0/200,425/400,0/2000
congestion = 425/500,0/500

[nl]
description = Netherlands
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/250,0/250
callwaiting = 425/500,0/9500
dialrecall = 425/500,0/50
record = 1400/500,0/15000
info = 950/330,1400/330,1800/330,0/1000
stutter = 425/500,0/50

[no]
description = Norway
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/200,0/200
callwaiting = 425/200,0/600,425/200,0/10000
dialrecall = 470/400,425/400
record = 1400/400,0/15000
info = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0
stutter = 470/400,425/400

[nz]
description = New Zealand
ringcadence = 400,200,400,2000
dial = 400
busy = 400/500,0/500
ring = 400+450/400,0/200,400+450/400,0/2000
congestion = 400/250,0/250
callwaiting = !400/200,!0/3000,!400/200,!0/3000,!400/200,!0/3000,!400/200
dialrecall = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400
record = 1400/425,0/15000
info = 400/750,0/100,400/750,0/100,400/750,0/100,400/750,0/400
stutter = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400
unobtainable = 400/75,0/100,400/75,0/100,400/75,0/100,400/75,0/400

[ph]
description = Philippines
ringcadence = 1000,4000
dial = 425
busy = 480+620/500,0/500
ring = 425+480/1000,0/4000
congestion = 480+620/250,0/250
callwaiting = 440/300,0/10000
dialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,0
stutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440

[pl]
description = Poland
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/500,0/500
callwaiting = 425/150,0/150,425/150,0/4000
dialrecall = 425/500,0/50
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000
stutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425

[pt]
description = Portugal
ringcadence = 1000,5000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/5000
congestion = 425/200,0/200
callwaiting = 440/300,0/10000
dialrecall = 425/1000,0/200
record = 1400/500,0/15000
info = 950/330,1400/330,1800/330,0/1000
stutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425

[ru]
description = Russian Federation / ex Soviet Union
ringcadence = 1000,4000
dial = 425
busy = 425/350,0/350
ring = 425/1000,0/4000
congestion = 425/175,0/175
callwaiting = 425/200,0/5000
record = 1400/400,0/15000
info = 950/330,1400/330,1800/330,0/1000
dialrecall = 425/400,0/40
stutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425

[se]
description = Sweden
ringcadence = 1000,5000
dial = 425
busy = 425/250,0/250
ring = 425/1000,0/5000
congestion = 425/250,0/750
callwaiting = 425/200,0/500,425/200,0/9100
dialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425
record = 1400/500,0/15000
info = !950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,0
stutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425

[sg]
description = Singapore
ringcadence = 400,200,400,2000
dial        = 425
ring        = 425*24/400,0/200,425*24/400,0/2000
busy        = 425/750,0/750
congestion  = 425/250,0/250
callwaiting = 425*24/300,0/200,425*24/300,0/3200
stutter     = !425/200,!0/200,!425/600,!0/200,!425/200,!0/200,!425/600,!0/200,!425/200,!0/200,!425/600,!0/200,!425/200,!0/200,!425/600,!0/200,425
info        = 950/330,1400/330,1800/330,0/1000
dialrecall  = 425*24/500,0/500,425/500,0/2500
record      = 1400/500,0/15000
nutone      = 425/2500,0/500
intrusion   = 425/250,0/2000
warning     = 425/624,0/4376
acceptance  = 425/125,0/125
holdinga    = !425*24/500,!0/500
holdingb    = !425/500,!0/2500

[th]
description = Thailand
ringcadence = 1000,4000
dial = 400*50
busy = 400/500,0/500
ring = 420/1000,0/5000
congestion = 400/300,0/300
callwaiting = 1000/400,10000/400,1000/400
dialrecall = 400*50/400,0/100,400*50/400,0/100
record = 1400/500,0/15000
info = 950/330,1400/330,1800/330
stutter = !400/200,!0/200,!400/600,!0/200,!400/200,!0/200,!400/600,!0/200,!400/200,!0/200,!400/600,!0/200,!400/200,!0/200,!400/600,!0/200,400

[uk]
description = United Kingdom
ringcadence = 400,200,400,2000
dial = 350+440
specialdial = 350+440/750,440/750
busy = 400/375,0/375
congestion = 400/400,0/350,400/225,0/525
specialcongestion = 400/200,1004/300
unobtainable = 400
ring = 400+450/400,0/200,400+450/400,0/2000
callwaiting = 400/100,0/4000
specialcallwaiting = 400/250,0/250,400/250,0/250,400/250,0/5000
creditexpired = 400/125,0/125
confirm = 1400
switching = 400/200,0/400,400/2000,0/400
info = 950/330,0/15,1400/330,0/15,1800/330,0/1000
record = 1400/500,0/60000
stutter = 350+440/750,440/750

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

[us-old]
description = United States Circa 1950/ North America
ringcadence = 2000,4000
dial = 600*120
busy = 500*100/500,0/500
ring = 420*40/2000,0/4000
congestion = 500*100/250,0/250
callwaiting = 440/300,0/10000
dialrecall = !600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,600*120
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,0
stutter = !600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,600*120

[tw]
description = Taiwan
ringcadence = 1000,4000
dial = 350+440
busy = 480+620/500,0/500
ring = 440+480/1000,0/2000
congestion = 480+620/250,0/250
callwaiting = 350+440/250,0/250,350+440/250,0/3250
dialrecall = 300/1500,0/500
record = 1400/500,0/15000
info = !950/330,!1400/330,!1800/330,0
stutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440

[ve]
description = Venezuela / South America
ringcadence = 1000,4000
dial = 425
busy = 425/500,0/500
ring = 425/1000,0/4000
congestion = 425/250,0/250
callwaiting = 400+450/300,0/6000
dialrecall = 425
record = 1400/500,0/15000
info = !950/330,!1440/330,!1800/330,0/1000

[za]
description = South Africa
; http://www.cisco.com/univercd/cc/td/doc/product/tel_pswt/vco_prod/safr_sup/saf02.htm
; (definitions for other countries can also be found there)
; Note, though, that South Africa uses two switch types in their network --
; Alcatel switches -- mainly in the Western Cape, and Siemens elsewhere.
; The former use 383+417 in dial, ringback etc.  The latter use 400*33
; I've provided both, uncomment the ones you prefer
ringcadence = 400,200,400,2000
; dial/ring/callwaiting for the Siemens switches:
dial = 400*33
ring = 400*33/400,0/200,400*33/400,0/2000
callwaiting = 400*33/250,0/250,400*33/250,0/250,400*33/250,0/250,400*33/250,0/250
; dial/ring/callwaiting for the Alcatel switches:
; dial = 383+417
; ring = 383+417/400,0/200,383+417/400,0/2000
; callwaiting = 383+417/250,0/250,383+417/250,0/250,383+417/250,0/250,383+417/250,0/250
congestion = 400/250,0/250
busy = 400/500,0/500
dialrecall = 350+440
record = 1400/500,0/10000
info = 950/330,1400/330,1800/330,0/330
stutter = !400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,400*33
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
        <sources>2</sources>
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
		$stream_type=$data[7]; //ogg/mp3
		$voip_password=$data[8]; //rand 16char
		$trunk=$data[9]; //yes/no
		$record=$data[10]; //yes/no
		$answer=$data[11]; //yes/no 
		if ($stream_type=="mp3"){
			$stream_path="/stream-".$cong_name;
			}else{
			$stream_path="/stream-".$cong_name.".ogg";
			}
		?>
		
<!--mount-<?PHP echo $cong_name; ?>-->
<mount>
	<mount-name><?PHP echo $stream_path; ?></mount-name>
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
		if ($stream_type=="mp3"){
			$stream_path="/stream-".$cong_name;
			}else{
			$stream_path="/stream-".$cong_name.".ogg";
			}
				if ($stream_type=="mp3"){
$bitrate=15+(3*$stream_quality);
$info4 = "<ezstream>
    <url>http://".$server_in.":".$port."/stream-".$cong_name."</url>
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
    <svrinfosamplerate>8000</svrinfosamplerate>
    <svrinfopublic>0</svrinfopublic>
</ezstream>";
$file=fopen('./config/asterisk-ices-'.$cong_name.'.xml','w');
			if(fputs($file,$info4)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'4</div>';
			}
$info5="#!/bin/sh\ncat /dev/fd/3 | ".$lame_bin." --preset cbr ".$bitrate." -r -m m -s 8.0 --bitwidth 16 - - | ".$ezstream_bin." -c ".$web_server_root."/kh-live/config/asterisk-ices-".$cong_name.".xml";
$file=fopen('./config/mp3stream-'.$cong_name.'.sh','w');
			if(fputs($file,$info5)){
			fclose($file);
			//the file needs to have exec rights to work as an agi script we might not need to give 5 to nobody
			chmod('./config/mp3stream-'.$cong_name.'.sh', 0755);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'5</div>';
			}
}else{			
$info4="<?xml version=\"1.0\"?>
<ices>
    <background>0</background>
    <logpath>/var/log/ices</logpath>
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
            <param name=\"rate\">8000</param>
            <param name=\"channels\">1</param>
            <param name=\"metadata\">0</param>
            <param name=\"metadatafilename\"> </param>
        </input>
        <instance>
            <hostname>".$server_in."</hostname>
            <port>".$port."</port>
            <password>".$voip_password."</password>
            <mount>/stream-".$cong_name.".ogg</mount>
            <yp>0</yp>
            <encode>  
                <quality>".$stream_quality."</quality>
                <samplerate>8000</samplerate>
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
	}
	
include "sip-gen.php";
include "alsa-gen.php";
include "iax-gen.php";

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
}
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){

ob_start();
       
        echo '<?PHP
	/**last change on : '.date("F d Y H:i:s").'**/
$version=\''.$gen_version.'\';
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
            echo "File saved successfully<br />" ;
            
            fclose ($fichier);
	    }else{
	    // error saving
	    }
	}
}
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
Server state :<br />enable testing functions - the meeting is faked live, all errors are displayed,the listening page doesnt refresh<br />
<select class="field_login" name="server_beta" >
<option value="false" <?PHP if ($server_beta=="false") echo 'selected=selected';?>>Production (voip enabled)</option>
<option value="stream" <?PHP if ($server_beta=="stream") echo 'selected=selected';?>>Production (no voip)</option>
<option value="true" <?PHP if ($server_beta=="true") echo 'selected=selected';?>>Testing</option>
<option value="master" <?PHP if ($server_beta=="master") echo 'selected=selected';?>>Master (only use on kh-live.co.za)</option>
</select><br />
Server_in : <br />adress to test if the server is live - localhost used for : icecast actions urls + ices instance + ezstream instance<br />
<input class="field_login" type="text" name="server_in" value="<?PHP echo $server_in;?>" /><br />
server_out : <br />Server name (must be the same as set on kh-live.co.za)<br />
<input class="field_login" type="text" name="server_out" value="<?PHP echo $server_out;?>" /><br />
mooo.com address : <br />
<input class="field_login" type="text" name="moo_adr" value="<?PHP echo @$moo_adr;?>" /><br />
moo_key : <br />api key for link up with mooo.com server<br />
<input class="field_login" type="text" name="moo_key" value="<?PHP echo @$moo_key;?>" /><br />
api_key : <br />api key for link up with main server<br />
<input class="field_login" type="text" name="api_key" value="<?PHP echo @$api_key;?>" /><br />
master_key : <br />key for ip synch with main server and pwd for icecast admin<br />
<input class="field_login" type="text" name="master_key" value="<?PHP echo @$master_key;?>" /><br />
web_server_root : <br />root for webserver with trailing /<br />
<input class="field_login" type="text" name="web_server_root" value="<?PHP echo $web_server_root;?>" /><br />
temp_dir : <br />temp directory /dev/shm with trailing / <br />
<input class="field_login" type="text" name="temp_dir" value="<?PHP echo $temp_dir;?>" /><br />
icecast_bin : <br />icecast binary name (icecast on alpine icecast2 on debian)<br />
<input class="field_login" type="text" name="icecast_bin" value="<?PHP echo $icecast_bin;?>" /><br />
port :<br />icecast port <br />
<input class="field_login" type="text" name="port" value="<?PHP echo $port;?>" /><br />
timer : <br />used to reload  meeting page<br />
<input class="field_login" type="text" name="timer" value="<?PHP echo $timer;?>" /><br />
timer_listen :<br />istening timer <br />
<input class="field_login" type="text" name="timer_listen" value="<?PHP echo $timer_listen;?>" /><br />
asterisk_bin : <br />asterisk binary path + file_name<br />
<input class="field_login" type="text" name="asterisk_bin" value="<?PHP echo $asterisk_bin;?>" /><br />
asterisk_spool : <br />asterisk spool folder with trailing /<br />
<input class="field_login" type="text" name="asterisk_spool" value="<?PHP echo $asterisk_spool;?>" /><br />
lame_bin :<br />lame binary path + file_name<br />
<input class="field_login" type="text" name="lame_bin" value="<?PHP echo $lame_bin;?>" /><br />
ezstream_bin : <br />ezstream binary path + file_name<br />
<input class="field_login" type="text" name="ezstream_bin" value="<?PHP echo $ezstream_bin;?>" /><br />
test_url : <br />test url ex kh.sinux.ch check if nslookup works<br />
<input class="field_login" type="text" name="test_url" value="<?PHP echo $test_url;?>" /><br />
test_ip :<br />local ip to ping<br />
<input class="field_login" type="text" name="test_ip" value="<?PHP echo $test_ip;?>" /><br />
Audio device :<br />select which input device to use on direct input<br />
<select class="field_login" name="server_audio" >
<option value="0">None</option>
<option value="alsa" <?PHP if ($server_audio=="alsa") echo 'selected=selected';?>>Alsa</option>
<option value="dsp" <?PHP if ($server_audio=="dsp") echo 'selected=selected';?>>Oss (/dev/dsp)</option>
</select><br />
Direct input hw :<br />hardware for input (default)<br />
<input class="field_login" type="text" name="alsa_in" value="<?PHP echo @$alsa_in;?>" /><br />
Direct output hw :<br />hardware for output (default)<br />
<input class="field_login" type="text" name="alsa_out" value="<?PHP echo @$alsa_out;?>" /><br />
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
Auto config freedns :<br />update ip address at freedns every 5min<br />
<select class="field_login" name="auto_dns" >
<option value="yes">Yes</option>
<option value="no" <?PHP if ($auto_dns=="no") echo 'selected=selected';?>>No</option>
</select><br />
Auto config kh-live.co.za :<br />update ip address at kh-live.co.za every 5min<br />and linkup users changes with kh-live.co.za<br />
<select class="field_login" name="auto_khlive" >
<option value="yes">Yes</option>
<option value="no" <?PHP if ($auto_khlive=="no") echo 'selected=selected';?>>No</option>
</select><br />
Mp3 encoder speed<br />no of seconds encoded in one second (26 for raspberry B+)<br />
<input class="field_login" type="text" name="encoder_speed" value="<?PHP echo @$encoder_speed;?>" /><br />
<input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
<hr />
Use this button to re-generate all the config files from db.<br />
<input type="submit" value="Over-write config!" onclick="javascript:redoconfig()" />
</div>