<?PHP	
ob_start();
?>; Asterisk alsa configuration file
[general]
autoanswer=yes
context=default
extension=1234
input_device=<?PHP echo $alsa_in ; ?> 
output_device=<?PHP echo $alsa_out ; ?> 

<?PHP
$message = ob_get_clean();
if ($server_audio=="alsa"){
$fichier = fopen('./config/alsa.conf', 'w');
}else{
$fichier = fopen('./config/oss.conf', 'w');
}
            if (fwrite($fichier, $message)){
	    exec($asterisk_bin.' -rx "core reload"');
		// success
	fclose ($fichier);
	    }else{
	echo "error saving alsa file<br />" ;
	    }
?>