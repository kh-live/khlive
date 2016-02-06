<?PHP
$max_song_no=150;
if(session_id()==""){session_start();}
if (isset($_GET['song_1'])){
	if ($_GET['song_1'] >=1 AND $_GET['song_1'] <=$max_song_no) $_SESSION['song_1']=$_GET['song_1'];
}
if (isset($_GET['song_2'])){
	if ($_GET['song_2'] >=1 AND $_GET['song_2'] <=$max_song_no) $_SESSION['song_2']=$_GET['song_2'];
}
if (isset($_GET['song_3'])){
	if ($_GET['song_3'] >=1 AND $_GET['song_3'] <=$max_song_no) $_SESSION['song_3']=$_GET['song_3'];
}
if (isset($_GET['play'])){
	if (is_numeric($_GET['play'])){
	if ($_GET['play'] >=1 AND $_GET['play'] <=$max_song_no) {
	exec("/usr/bin/mocp -x");
	exec("/usr/bin/mocp -S");
	exec("/usr/bin/mocp -l /var/www/kh-live/kh-songs/iasn_E_".$_GET['play'].".m4a");
	if(strstr($_SESSION['meeting_status'],"live")){
	echo "Playing...";
	}else{
	echo "not_live";
	}
	}
	}elseif ($_GET['play']=="rand"){
	exec("/usr/bin/mocp -x");
	echo "Playing Random...";
	exec("/usr/bin/mocp -S");
	exec("/usr/bin/mocp -c");
	exec("/usr/bin/mocp -a /var/www/kh-live/kh-songs/");
	exec("/usr/bin/mocp -t shuffle");
	exec("/usr/bin/mocp -p");
	}
}
if (isset($_GET['stop'])){
	
	if ($_GET['stop']=="true") {
	exec("/usr/bin/mocp -x");
	echo "Stopped...";
	}
}
if (isset($_GET['pause'])){
	
	if ($_GET['pause']=="true") {
	exec("/usr/bin/mocp -G");
	echo "Paused...";
	}
}
?>