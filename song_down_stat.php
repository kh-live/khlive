<?PHP
include "db/config.php";
if (isset($_GET['song_no'])){
	if (is_numeric($_GET['song_no'])){
		if ($_GET['song_no']<=$max_song_no AND $_GET['song_no']>=0){
			if (isset($_GET['type'])){
				if ($_GET['type']=='piano' OR $_GET['type']=='orchestral' OR $_GET['type']=='new' OR $_GET['type']=='joy' OR $_GET['type']=='vid240' OR $_GET['type']=='vid480' OR $_GET['type']=='vid360' OR $_GET['type']=='vid720'){
		set_time_limit (15 * 60);
		$song_no=$_GET['song_no'];
        if ($_GET['song_no']<=99) $song_no='0'.$_GET['song_no'];
       if ($_GET['song_no']<=9) $song_no='00'.$_GET['song_no'];
		$file_name='';
		if ($_GET['type']=='piano') $file_name='iasn_E_'.$song_no.'.mp3';
		if ($_GET['type']=='orchestral') $file_name='iasnm_E_'.$song_no.'.mp3';
		if ($_GET['type']=='new') $file_name='snnw_E_'.$song_no.'.mp3';
		if ($_GET['type']=='joy') $file_name='sjjm_E_'.$song_no.'.mp3';
		if ($_GET['type']=='vid'.$song_quality) $file_name='sjjm_E_'.$song_no.'_r'.$song_quality.'P.mp4';
		if ($file_name!=''){
		echo round(filesize ("./kh-songs/".$file_name) / 1024,0);
		}	
				}
			}
		}
	}
}
?>