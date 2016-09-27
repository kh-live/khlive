<?PHP
include 'db/config.php';
if (isset($scheduler)){
	if ($$scheduler=='yes'){
	echo 'scheduler worked at '.date('H-i-s',time());
	}
}
?>