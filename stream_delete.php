<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['delete']){
		if ($_POST['stream_confirmed']!=""){
			$stream_confirmed=urldecode($_POST['stream_confirmed']);
			$db=file("db/streams");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$stream_confirmed){
		$cong=$data[1];
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/streams','w');
			if(fputs($file,$file_content)){
			fclose($file);
			
			$db=file("config/icecast.xml");
			$file_content="";
			$line_to_skip=0;
	foreach($db as $line){
		if (strstr($line,'<!--mount-'.$cong.'-->')){
		 $line_to_skip=12;
		}elseif($line_to_skip>>0){
		$line_to_skip--;
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/icecast.xml','w');
			if(fputs($file,$file_content)){
			fclose($file);
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
	}
}
if(isset($_GET['stream'])){
$stream=$_GET['stream']; //sanitize input
?>
<div id="page">
<h2><?PHP echo $lng['streams'];?></h2>
<?PHP echo $lng['remove_stream'];?><br /><br />
<form action="./stream_delete" method="post">
<b><?PHP echo $lng['stream'];?></b>: <?PHP echo $stream;?><br />
<input type="hidden" name="stream_confirmed" value="<?PHP echo $stream;?>"><br />

<a href="./streams"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['streams'];?></h2>
Click <a href="./streams">here</a> to edit more streams.<br /><br />
</div>
<?PHP
}
?>