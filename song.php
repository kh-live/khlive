<?PHP
if(session_id()==""){session_start();}
if (isset($_GET['song_1'])){
	if ($_GET['song_1'] >=1 AND $_GET['song_1'] <=135) $_SESSION['song_1']=$_GET['song_1'];
}
if (isset($_GET['song_2'])){
	if ($_GET['song_2'] >=1 AND $_GET['song_2'] <=135) $_SESSION['song_2']=$_GET['song_2'];
}
if (isset($_GET['song_3'])){
	if ($_GET['song_3'] >=1 AND $_GET['song_3'] <=135) $_SESSION['song_3']=$_GET['song_3'];
}
?>