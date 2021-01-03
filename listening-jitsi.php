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
<script>
const domain = '<?PHP echo $jitsi_url; ?>';
const options = {
	roomName: 'Kh-Live-<?PHP echo $_SESSION['cong']; ?>-<?PHP echo date("Ymd",time()) ?>',
	width: 700,
	height: 700,
	parentNode: document.querySelector('#jitsiFrame'),
	userInfo: {
		displayName: '<?PHP echo $_SESSION['full_name']; ?>'
    }
};
const api = new JitsiMeetExternalAPI(domain, options);

</script>
