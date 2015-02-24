<?PHP
if(session_id()==""){session_start();}
if (isset($_GET['song_1'])){
	if ($_GET['song_1'] >=1 AND $_GET['song_1'] <=138) $_SESSION['song_1']=$_GET['song_1'];
}
if (isset($_GET['song_2'])){
	if ($_GET['song_2'] >=1 AND $_GET['song_2'] <=138) $_SESSION['song_2']=$_GET['song_2'];
}
if (isset($_GET['song_3'])){
	if ($_GET['song_3'] >=1 AND $_GET['song_3'] <=138) $_SESSION['song_3']=$_GET['song_3'];
}
if (isset($_GET['play'])){
	if (is_numeric($_GET['play']){
	if ($_GET['play'] >=1 AND $_GET['play'] <=138) {
	$no=$_GET['play'];
	if ($no<=9){
	$no="00"+$no;
	}elseif ($no<=99){
	$no="0"+$no;
	}
	exec("sox /var/www/kh-live/kh-songs/iasn_E_".$no.".m4a");
	echo "Playing..."
	}
	}
}
if (isset($_GET['stop'])){
	
	if ($_GET['stop']=="true") {
	$no=$_GET['play'];

	exec("kill $(pidof play)");
	echo "Stopped..."
	}
}
?>