<?PHP
//we need a button to start the meeting otherwise it will automatically start when this page is loaded
//we mustnt forget to set the var meeting live once started
//and have a button to stop the meeting

$jitsi_url=$jitsi_address;
if (isset($jitsi_cong_address)){
	if ($jitsi_cong_address!=''){
		$jitsi_url=$jitsi_cong_address;
	}
}

?>
<script src='https://meet.jit.si/external_api.js'></script>
<div style="height:700px;width:700px;" id="jitsiFrame"></div>
<button id="start" type="button">Start</button>
<script>
var button = document.querySelector('#start');
var api = null;
button.addEventListener('click', khInitiateJitsi );
function khInitiateJitsi(){
	var domain = '<?PHP echo $jitsi_url; ?>';
	var options = {
		roomName: 'Kh-Live-<?PHP echo $_SESSION['cong']; ?>-<?PHP echo date("Ymd",time()) ?>',
		width: 700,
		height: 700,
		parentNode: document.querySelector('#jitsiFrame'),
		userInfo: {
			displayName: '<?PHP echo $_SESSION['full_name']; ?>'
	    }
	};
	api = new JitsiMeetExternalAPI(domain, options);
	khMeetingStart();
}
function khMeetingStart(){
//ajax call to stream_start.php
//POST 
//$action=mount_add
//$mount=$_POST['mount']; used to define cong
//	$server=$_POST['server'];
//	$port
var button = document.querySelector('#start');
var xhttp = new XMLHttpRequest();
xhttp.open("POST", "stream_start.php", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("action=mount_add&mount=/stream-<?PHP echo urlencode($_SESSION['cong']); ?>.ogg&server=<?PHP echo urlencode($jitsi_url); ?>&port=Kh-Live-<?PHP echo urlencode($_SESSION['cong']); ?>-<?PHP echo date("Ymd",time()) ?>"); 
button.innerHTML="Stop";
button.removeEventListener('click', khInitiateJitsi);
button.addEventListener('click', khMeetingStop );
}
function khMeetingStop(){
//ajax call to stream_end.php
//POST 
//$action=mount_remove
//$mount=$_POST['mount']; used to define cong
//	$server=$_POST['server'];
//	$port
var button = document.querySelector('#start');
var xhttp = new XMLHttpRequest();
xhttp.open("POST", "stream_end.php", true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("action=mount_remove&mount=/stream-<?PHP echo urlencode($_SESSION['cong']); ?>.ogg&server=<?PHP echo urlencode($jitsi_url); ?>&port=Kh-Live-<?PHP echo urlencode($_SESSION['cong']); ?>-<?PHP echo date("Ymd",time()) ?>"); 

api.dispose();
button.innerHTML="Re-Start";
button.removeEventListener('click', khMeetingStop );
button.addEventListener('click', khInitiateJitsi );
}

</script>



