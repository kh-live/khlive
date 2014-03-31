<?PHP
/*first we need to know what the situation was when the script ended last
check /dev/shm
if the variables dont exist, it means the server rebooted
create the variables make sure that we dont reboot this time already to avoid reboot loops*/
if (!file_exists('/dev/shm/status')){
$file=fopen('/dev/shm/status','w');
	fputs($file,"startup");
	fclose($file);
	
	//we should also log in file
}
$last_status=implode(file('/dev/shm/status'));

/*if everything is ok do nothing and check it's still ok*/
$dns_result=gethostbyname("kh.sinux.ch");
	if (strstr($dns_result, "kh.sinux.ch")){
		$dns_result=gethostbyname("www.google.com");
		if (strstr($dns_result, "www.google.com")){
	//the dns doesnt resolve -> no internet connection we need to know what's wrong
		$status='no_dns';
	}else{
	//it could also be that ns.sinux.ch is down or dyndns is down
		$status='no_ns';
	}
	
		exec('ping -c 1 4.2.2.2',$ping_result);
		$ping_result=implode(" , ",$ping_result);
		if (strstr($ping_result, "1 received")){
		//tcp connection is still on, so it might be a dns server's failure
		}else{
		//no dns and no tcp connection
		$status='no_tcp';
		}
	}else{
	$status='ok'
	}
/*if there was no connection last time, see what we can do to improve the situation
*/
if ($status=='ok'){

/*check if the dns points to the right ip address
if not update moo.com et sinux.ch/kh.php
maybe run a script on sinux.ch to check if the connectivity is not one sided*/
}else{
//there was a problem the round before already and restarting wvdial didnt solve it we must do something more radical
	if ($last_status=='no_tcp' OR $last_status=='no_dns'){
			//let's check thaht there is no meeting on so we don't reboot in the middle of one
			
			// we disconnect the usb
			exec('echo "1-3" > /sys/bus/usb/drivers/usb/unbind');
			//we reboot
			exec('echo "1-3" > /sys/bus/usb/drivers/usb/bind');
		
	}else{
	//this is the first time there is a problem
		exec('ps aux',$ps_result);
		$ps_result=implode(" , ",$ps_result);
		if (strstr($ps_result, "wvdial")){
		//wvdial is running but there is no connection
			exec('kill $(pidof wvdial)');
			// we might need to wait before we restart wvdial
			exec('wvdial');
		}else{
		//wvdial isnt running
			exec('wvdial');
		}
	}

}

/*connectivity check and restore

is vwdial running
if not restart it
but that might not be enough the dongle might be stuck
check if restarting vwdial doesn't work and reboot the server if it's not during a meeting

check if there is ip connectivity
if not kill wvdial and restart it*/
//ping takes too long when it fails and is inaccurate


/*check if there is dns resolving
if not kill vwdial and restart it*/




/*optional check if the iax trunking is connected
if not reload iax2
exec('asterisk -rx "iax2 show registry"',$iax_result);
	$iax_result=implode(" , ",$iax_result);
	if (strstr($iax_result, "Registered")){
	$iax='<b style="color:green;">connected</b>';
	}else{
	$iax='<b style="color:red;">disconnected</b><br /><i style="font-size:12px;background-color:grey;">'.$iax_result.'</i>';
	}*/
// we push the status of the script to memory so we know what happened next time
$file=fopen('/dev/shm/status','w');
	fputs($file,$status);
	fclose($file);
?>