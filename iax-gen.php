<?PHP	
ob_start();
?>[general]
autokill=yes
srvlookup=yes

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
		if ($voip_type=="iax"){
?>
[<?PHP echo $phone_no; ?>]
type=friend
context=default
host=dynamic
username=<?PHP echo $phone_no; ?> 
secret=<?PHP echo $voip_password; ?> 
disallow=all
allow=ulaw
allow=alaw
allow=gsm
requirecalltoken=no
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
		if ($type=="iax" OR $type=="all"){
?>
[<?PHP echo $user_name; ?>]
type=friend
context=default
host=dynamic
username=<?PHP echo $user_name; ?> 
secret=<?PHP echo $pin; ?> 
disallow=all
allow=ulaw
allow=alaw
allow=gsm
requirecalltoken=no
<?PHP
		}
}
	 	          $message = ob_get_clean();
$fichier = fopen('./config/iax.conf', 'w');
            if (fwrite($fichier, $message)){
	    exec($asterisk_bin.' -rx "iax2 reload"');
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
?>