<?PHP
//this file contains all the asterisk specific configuration files.
//it is deprecated since we are not using asterisk anymore

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
	    
	    $db=file("db/cong");
    foreach($db as $line){
        $data=explode ("**",$line);
		$cong_name=$data[0];
		$stream=$data[6]; //yes/no
if ($stream=='yes'){
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
?>