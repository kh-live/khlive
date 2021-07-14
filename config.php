<?PHP
$tmp_skip='no';
$gen_version='3.0.3';//gen_version leave this comment it's used in auto_update
$max_song_no=154;
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
if (isset($_GET['action'])){
	if ($_GET['action']=="ok"){
	
	if ($server_beta=="false"){
	//if we are still using asterisk we need to generate the config files
	include ('config-asterisk.php');
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

  current=$(wget -q -O - https://kh-live.co.za/ip.php|sed s/[^0-9.]//g)
       [ "$current" != "$registered" ] && {
wget -q -O /dev/null $UPDATEURL
           }
	   
<?PHP }
if ($auto_khlive=="yes") echo 'wget -q -O /dev/null http://'.$server_in.'/kh-live/update_ip.php'; ?>

<?PHP
//this is where we can auto setup ssl-certificate
if ($enable_ssl=="setup"){
?>
CERTBOT=$(which certbot)
PROCVER=$(uname -m)
if [ "${#CERTBOT}"==0 ]
then
apt-get update
apt-get install python-certbot-apache -y
certbot --noninteractive --apache --agree-tos --no-redirect --register-unsafely-without-email -d <?PHP echo $server_out; //leave next line empty otherwise carriage return doesn work ?>

cat /etc/letsencrypt/live/<?PHP echo $server_out; ?>/fullchain.pem /etc/letsencrypt/live/<?PHP echo $server_out; ?>/privkey.pem > /etc/icecast2/bundle.pem
sed -i -e '$apost_hook = cat /etc/letsencrypt/live/<?PHP echo $server_out; ?>/fullchain.pem /etc/letsencrypt/live/<?PHP echo $server_out; ?>/privkey.pem > /etc/icecast2/bundle.pem && service icecast2 restart' /etc/letsencrypt/renewal/<?PHP echo $server_out; ?>.conf

if [ $PROCVER = "armv6l" ]
then
(cd /home/pi && wget https://kh-live.co.za/downloads/icecast2-armv6.zip)
(cd /home/pi && unzip icecast2-armv6.zip)
rm /home/pi/icecast2-armv6.zip
elif [ $PROCVER = "armv7l" ]
then
(cd /home/pi && wget https://kh-live.co.za/downloads/icecast2-armv7.zip)
(cd /home/pi && unzip icecast2-armv7.zip)
rm /home/pi/icecast2-armv7.zip
else
#build from source
fi
mv /usr/bin/icecast2 /usr/bin/icecast2nossl
mv /home/pi/icecast2 /usr/bin/icecast2
chmod +x /usr/bin/icecast2
/sbin/reboot
fi

<?PHP }
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
<?PHP
if ($enable_ssl=="no" OR $enable_ssl=="" ){
?>
    <listen-socket>
        <port><?PHP echo $port; ?></port>
    </listen-socket>
<?PHP
}elseif ($enable_ssl=="force"){
//we still need to have both ports open as locally ices or ezstream use port 8000
?>
    <listen-socket>
        <port><?PHP echo $port; ?></port>
    </listen-socket>
    <listen-socket>
        <port><?PHP echo $icecast_ssl_port; ?></port>
	<ssl>1</ssl>
    </listen-socket>
<?PHP
}else{
//this is auto
?>
    <listen-socket>
        <port><?PHP echo $port; ?></port>
    </listen-socket>
        <listen-socket>
        <port><?PHP echo $icecast_ssl_port; ?></port>
	<ssl>1</ssl>
    </listen-socket>
<?PHP
}
?>
    <fileserve>1</fileserve>
    <paths>
       <logdir>/var/log/<?PHP echo $icecast_bin; ?></logdir>
        <webroot>/usr/share/<?PHP echo $icecast_bin; ?>/web</webroot>
        <adminroot>/usr/share/<?PHP echo $icecast_bin; ?>/admin</adminroot>
        <pidfile>/var/run/<?PHP echo $icecast_bin; ?>/icecast.pid</pidfile>
	<alias source="/" dest="/status.xsl"/>
	<?PHP
if ($enable_ssl=="force" OR $enable_ssl=="auto"){
?>
	<ssl-certificate>/etc/icecast2/bundle.pem</ssl-certificate>
<?PHP
}
?>
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
	    //this doesnt work... asterisk user doesn't have the rights to kill...
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
            echo "<div id=\"page\"><br /><b style=\"color:green;\">Configuration saved successfully! </b><br />Don't forget to apply the changes.<br />";
	?>   <script>
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
<input type="submit" value="Apply configuration changes" onclick="javascript:redoconfig()" /></div></body></html>
            <?PHP
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
connect using https://<br />only use http if you're php version is less than 7.1<br />php version : <?PHP echo phpversion(); ?><br />
<select class="field_login" name="https" >
<option value="https">https://</option>
<option value="http" <?PHP if (@$https=="http") echo 'selected=selected';?>>http://</option>
</select><br />
TTL default<br />default timetolive in seconds for backend connection<br />
<?PHP
if (!isset($ttl_back)){
$ttl_back="5";
}
?>
<select class="field_login" name="ttl_back" >
<option value="0">no ttl</option>
<option value="1" <?PHP if (@$ttl_back=="1") echo 'selected=selected';?>>1</option>
<option value="2" <?PHP if (@$ttl_back=="2") echo 'selected=selected';?>>2</option>
<option value="3" <?PHP if (@$ttl_back=="3") echo 'selected=selected';?>>3</option>
<option value="4" <?PHP if (@$ttl_back=="4") echo 'selected=selected';?>>4</option>
<option value="5" <?PHP if (@$ttl_back=="5") echo 'selected=selected';?>>5</option>
<option value="6" <?PHP if (@$ttl_back=="6") echo 'selected=selected';?>>6</option>
<option value="7" <?PHP if (@$ttl_back=="7") echo 'selected=selected';?>>7</option>
<option value="8" <?PHP if (@$ttl_back=="8") echo 'selected=selected';?>>8</option>
<option value="9" <?PHP if (@$ttl_back=="9") echo 'selected=selected';?>>9</option>
<option value="10" <?PHP if (@$ttl_back=="10") echo 'selected=selected';?>>10</option>
</select><br />
TTL homepage<br />default timetolive in seconds for frontend connection<br />
<?PHP
if (!isset($ttl_front)){
$ttl_front="3";
}
?>
<select class="field_login" name="ttl_front" >
<option value="0">no ttl</option>
<option value="1" <?PHP if (@$ttl_front=="1") echo 'selected=selected';?>>1</option>
<option value="2" <?PHP if (@$ttl_front=="2") echo 'selected=selected';?>>2</option>
<option value="3" <?PHP if (@$ttl_front=="3") echo 'selected=selected';?>>3</option>
<option value="4" <?PHP if (@$ttl_front=="4") echo 'selected=selected';?>>4</option>
<option value="5" <?PHP if (@$ttl_front=="5") echo 'selected=selected';?>>5</option>
<option value="6" <?PHP if (@$ttl_front=="6") echo 'selected=selected';?>>6</option>
<option value="7" <?PHP if (@$ttl_front=="7") echo 'selected=selected';?>>7</option>
<option value="8" <?PHP if (@$ttl_front=="8") echo 'selected=selected';?>>8</option>
<option value="9" <?PHP if (@$ttl_front=="9") echo 'selected=selected';?>>9</option>
<option value="10" <?PHP if (@$ttl_front=="10") echo 'selected=selected';?>>10</option>
</select><br />
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

<div class="subgroup" onclick="javascript:toogleDiv(6)">Video interface</div>
<div class="subgroups" id="subgroup6">
Default Jitsi videobridge address :<br />address of the server hosting the conference (meet.jit.si)<br />
<?PHP
if (!isset($jitsi_address)){
$jitsi_address="meet.jit.si";
}
?>
<input class="field_login" type="text" name="jitsi_address" value="<?PHP echo @$jitsi_address;?>" /><br />
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
<div class="subgroup" onclick="javascript:toogleDiv(10)">SSL</div>
<div class="subgroups" id="subgroup10">

<?PHP
exec ('which certbot', $test_exec);
if (strstr(implode('',$test_exec), 'certbot') OR $server_beta=="master"){
?>
<i style="color:green;">It seems SSL is installed, you can try to enable it below.</i><br /><br />
Enable SSL:<br />no=will redirect https requests to http <br />force=will redirect http requests to https <br />auto=lets the user decide which protocol to use<br />
<select class="field_login" name="enable_ssl" >
<option value="no" <?PHP if (@$enable_ssl=="no") echo 'selected=selected';?>>no (default)</option>
<option value="force" <?PHP if (@$enable_ssl=="force") echo 'selected=selected';?>>force</option>
<option value="auto" <?PHP if (@$enable_ssl=="auto") echo 'selected=selected';?>>auto</option>
</select><br />
<?PHP
}else{
?>
<i style="color:red;">Before enabling SSL, you need to have a valid certificate installed (see installation procedure <a href="http://wiki.kh-live.co.za/doku/doku.php?id=ssl" target="_blank">here</a> )</i><br /><br />
Auto setup SSL:<br />no=will do nothing <br />setup= will try to auto install SSL<br />
<select class="field_login" name="enable_ssl" >
<option value="no" <?PHP if (@$enable_ssl=="no") echo 'selected=selected';?>>no (default)</option>
<option value="setup" <?PHP if (@$enable_ssl=="setup") echo 'selected=selected';?>>setup</option>
</select><br />
<?PHP
}
if (!isset($icecast_ssl_port)){
$icecast_ssl_port="8443";
}
if (!isset($apache_ssl_port)){
$apache_ssl_port="443";
}
?>
Icecast SSL port :<br />default 8443 <br />
<input class="field_login" type="text" name="icecast_ssl_port" value="<?PHP echo $icecast_ssl_port;?>" /><br />
Apache SSL port :<br />default 443 - this doesn't change the port in apache configuration, but is used to generate the correct links<br />
<i style="color:red;">If you decide to use a non standard port, the SSL certificate won't renew automatically!</i><br /><br />
<input class="field_login" type="text" name="apache_ssl_port" value="<?PHP echo $apache_ssl_port;?>" /><br />
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
