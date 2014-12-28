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
$fichier = fopen('./config/alsa.conf', 'w');
            if (fwrite($fichier, $message)){
	    exec($asterisk_bin.' -rx "core reload"');
            echo "File saved successfully<br />" ;
	fclose ($fichier);
	    }else{
	    // error saving
	    }
?>