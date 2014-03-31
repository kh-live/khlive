<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['delete']){
		if ($_POST['cong_confirmed']!=""){
			$cong_confirmed=urldecode($_POST['cong_confirmed']);//sanitize
			$db=file("db/cong");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[0]==$cong_confirmed){
		$cong_no=@$data[1];
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('./db/cong','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			$db=file("config/meetme.conf");
			$file_content="";
	foreach($db as $line){
		if (strstr($line,$cong_no)){
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/meetme.conf','w');
			if(fputs($file,$file_content)){
			fclose($file);
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
			
			$db=file("db/streams");
			$file_content="";
	foreach($db as $line){
        $data=explode ("**",$line);
		if ($data[1]==$cong_confirmed){
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
		if (strstr($line,'<!--mount-'.$cong_confirmed.'-->')){
		 $line_to_skip=12;
		}elseif($line_to_skip>>0){
		$line_to_skip--;
		}else{
		$file_content.=$line;
		}
		//fix this algo so it's not dependent on the amount of lines
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
			
			$db=file("config/extensions_custom.conf");
			$file_content="";
			$skip=0;
	foreach($db as $line){
		if (strstr($line,';'.$cong_confirmed.'-start')){
		 $skip=1;
		 }elseif(strstr($line,';'.$cong_confirmed.'-stop')){
		 $skip=0;
		}elseif($skip==1){
		//do nothing
		}else{
		$file_content.=$line;
		}
	}
			$file=fopen('config/extensions_custom.conf','w');
			if(fputs($file,$file_content)){
			fclose($file);
			unlink('config/asterisk-ices-'.$cong_confirmed.'.xml');
			unlink('config/stream_'.$cong_confirmed.'.call');
			echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
			}else{
			echo '<div id="error_msg">'.$lng['error'].'</div>';
			}
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
	}
}
/*
we must still delete the logs and the recordings. plus we must check if there is no users associated with the cong. we must also get the code more readable avoid long if{}  etc...
*/
if(isset($_GET['cong'])){
$cong=$_GET['cong']; //sanitize input
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
<?PHP echo $lng['remove_cong'];?><br /><br />
<form action="./cong_delete" method="post">
<b><?PHP echo $lng['congregation'];?></b>: <?PHP echo $cong;?><br />
<input type="hidden" name="cong_confirmed" value="<?PHP echo $cong;?>"><br />

<a href="./congregations"><?PHP echo $lng['cancel'];?></a> <input name="submit" id="input_login" type="submit" value="<?PHP echo $lng['delete'];?>">
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2><?PHP echo $lng['congregations'];?></h2>
Click <a href="./congregations">here</a> to edit more congregations.<br /><br />
</div>
<?PHP
}
?>