<?PHP
ob_start();
?>[general]
context=from-sip-external
allowguest=yes
srvlookup=yes
udpbindaddr=0.0.0.0
tcpenable=no

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
		$stream_quality=$data[12];
		if ($voip_type=="sip"){
?>
[<?PHP echo $phone_no; ?>]
type=friend
context=default
host=dynamic
nat=no
secret=<?PHP echo $voip_password; ?> 
dtmfmode=auto
disallow=all
allow=ulaw
allow=alaw

<?PHP
		}
}
	$db=file("db/users");
    foreach($db as $line){
        $data=explode ("**",$line);
		$user_name=$data[0];
		$password=$data[1];
		$full_name=$data[2];
		$cong_name=$data[3];
		$rights=$data[4];
		$pin=$data[5]; 
		$type=$data[6]; 
		$last_login=$data[7]; 
		$info=$data[8]; 
		if ($type=="sip" OR $type=="all"){
?>
[<?PHP echo $user_name; ?>]
type=friend
context=default
host=dynamic
nat=no
secret=<?PHP echo $pin; ?> 
dtmfmode=auto
disallow=all
allow=ulaw
allow=alaw

<?PHP
		}
}
	 	          $message = ob_get_clean();
$fichier = fopen('./config/sip.conf', 'w');
            if (fwrite($fichier, $message)){
		exec($asterisk_bin.' -rx "sip reload"');
        //   File saved successfully
	fclose ($fichier);
	    }else{
	    echo "error saving sip file";
	    }