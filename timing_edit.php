<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
include 'functions.php';
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
		if ($_POST['timing_id']!=""){
		//start cong del
			$id_confirmed=urldecode($_POST['timing_id']);//sanitize
$deleting=timing_del($id_confirmed);
if ($deleting=='ok'){
$timings=array();
		if ($_POST['name']!=""){
		//we obviously need to check the input
			$name=$_POST['name']; //check
			foreach ($_POST as $key=>$value){
			if ($key!="submit" AND $key!="name" AND $key!="timing_id"){
			$timings[$key]=$value;
			}
			}
		
$adding=timing_add($name,$timings);
}
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**timing edit successful**'.$name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**timing edit add fail**'.$name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
}else{
echo $deleting;
$info=time().'**error**timing edit del fail**'.$name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}	
			
		}else{
		echo '<div id="error_msg">'.$lng['error'].'</div>';
		}
	}
}
if(isset($_GET['id'])){
$id=urldecode($_GET['id']); //sanitize input
$db=file("db/sched");
$i=0;
    foreach($db as $line){
        $data=explode ("**",$line);
	if ($i==$id) {
	$cong_selected=$data[0];
	$day=$data[1];
	$start_time=explode(':',$data[2]);
	$start_time_hour=$start_time[0];
	$start_time_min=$start_time[1];
	$stop_time=explode(':',$data[3]);
	$stop_time_hour=$stop_time[0];
	$stop_time_min=$stop_time[1];
	$enabled=$data[4];
	}
	$i++;
	}
	
?>
<div id="page">
<h2>Timings</h2>
Edit timing<br /><br />
<script type="text/javascript">
var id=1
	function addPart(){
	id++;
	<?PHP
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("name'.$i.'")!==null){
	var name'.$i.'=document.getElementById("name'.$i.'").value;
	}';
	$i++;
	}
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("length'.$i.'")!==null){
	var length'.$i.'=document.getElementById("length'.$i.'").value;
	}';
	$i++;
	}
	?>
	document.getElementById("parts").innerHTML+="<input class=\"add_part\" type=\"button\" value=\"Add a part here\" onclick=\"javascript:addBefore("+id+")\" /><br /><b>Part "+id+"</b>( <a href=\"javascript:removePart("+id+")\">X remove part "+id+"</a> )<br />name : <input type=\"text\" id=\"name"+id+"\" name=\"name"+id+"\" /><br /> length : <input type=\"text\" name=\"length"+id+"\"  id=\"length"+id+"\" />min<br /><br />";
	<?PHP
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("name'.$i.'")!==null){
	if (typeof name'.$i.'!== "undefined"){
	document.getElementById("name'.$i.'").value=name'.$i.';
	}
	}';
	$i++;
	}
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("length'.$i.'")!==null){
	if (typeof length'.$i.'!== "undefined"){
	document.getElementById("length'.$i.'").value=length'.$i.';
	}
	}';
	$i++;
	}
	?>
	}
	function addBefore(tmp){
	id++;
	<?PHP
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("name'.$i.'")!==null){
	var name'.$i.'=document.getElementById("name'.$i.'").value;
	}';
	$i++;
	}
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("length'.$i.'")!==null){
	var length'.$i.'=document.getElementById("length'.$i.'").value;
	}';
	$i++;
	}
	?>
	document.getElementById("parts").innerHTML+="<input class=\"add_part\" type=\"button\" value=\"Add a part here\" onclick=\"javascript:addBefore("+id+")\" /><br /><b>Part "+id+"</b>( <a href=\"javascript:removePart("+id+")\">X remove part "+id+"</a> )<br />name : <input type=\"text\" id=\"name"+id+"\" name=\"name"+id+"\" /><br /> length : <input type=\"text\" name=\"length"+id+"\"  id=\"length"+id+"\" />min<br /><br />";
	<?PHP
	$i=1;
	while ($i<50){
	echo '
	if (document.getElementById("name'.$i.'")!==null){
	if (typeof name'.$i.'!== "undefined"){
	if (tmp>'.$i.'){
	document.getElementById("name'.$i.'").value=name'.$i.';
	}
	if (tmp<='.$i.'){
	document.getElementById("name'.($i+1).'").value=name'.$i.';
	}
	if (tmp=='.$i.'){
	document.getElementById("name'.$i.'").value="";
	}
	}
	}';
	$i++;
	}
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("length'.$i.'")!==null){
	if (typeof length'.$i.'!== "undefined"){
	if (tmp>'.$i.'){
	document.getElementById("length'.$i.'").value=length'.$i.';
	}
	if (tmp<='.$i.'){
	document.getElementById("length'.($i+1).'").value=length'.$i.';
	}
	if (tmp=='.$i.'){
	document.getElementById("length'.$i.'").value="";
	}
	}
	}';
	$i++;
	}
	?>
	}
		function removePart(tmp){
	id--;
	<?PHP
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("name'.$i.'")!==null){
	var name'.$i.'=document.getElementById("name'.$i.'").value;
	}';
	$i++;
	}
	$i=1;
	while ($i<50){
	echo 'if (document.getElementById("length'.$i.'")!==null){
	var length'.$i.'=document.getElementById("length'.$i.'").value;
	}
	';
	$i++;
	}

	echo 'document.getElementById("parts").innerHTML="";
	';
	$i=1;
	while ($i<50){
	echo '
	
		if (id>='.$i.'){
		if (typeof name'.$i.'!== "undefined"){
	if (tmp>'.$i.'){
	document.getElementById("parts").innerHTML+="<input class=\"add_part\" type=\"button\" value=\"Add a part here\" onclick=\"javascript:addBefore('.$i.')\" /><br /><b>Part '.$i.'</b>( <a href=\"javascript:removePart('.$i.')\">X remove part '.$i.'</a> )<br />name : <input type=\"text\" id=\"name'.$i.'\" name=\"name'.$i.'\" /><br /> length : <input type=\"text\" name=\"length'.$i.'\"  id=\"length'.$i.'\" />min<br /><br />";
	}
	if (tmp<='.$i.'){
	document.getElementById("parts").innerHTML+="<input class=\"add_part\" type=\"button\" value=\"Add a part here\" onclick=\"javascript:addBefore('.$i.')\" /><br /><b>Part '.$i.'</b>( <a href=\"javascript:removePart('.$i.')\">X remove part '.$i.'</a> )<br />name : <input type=\"text\" id=\"name'.$i.'\" name=\"name'.$i.'\" /><br /> length : <input type=\"text\" name=\"length'.$i.'\"  id=\"length'.$i.'\" />min<br /><br />";
	}

	
	}
	}';
	$i++;
	}
	$i=1;
	while ($i<50){
	echo '
		if (id>='.$i.'){
		if (typeof name'.$i.'!== "undefined"){
	if (tmp>'.$i.'){
	document.getElementById("name'.$i.'").value=name'.$i.';
	document.getElementById("length'.$i.'").value=length'.$i.';
	}
	if (tmp<='.$i.'){
	document.getElementById("name'.$i.'").value=name'.($i+1).';
	document.getElementById("length'.$i.'").value=length'.($i+1).';
	}

	
	}
	}';
	$i++;
	}
	?>
	}
	</script>
<form action="./timing_edit" method="post">
<b>Name</b><br />
<p>Please note that if you change the name, you'll have to update the link on the scheduler edit page</p>
<?PHP
$i=0;
if (isset($_GET['id'])){
$timing_id=$_GET['id'];
}else{
die('error timing id must be set');
}

$db=file("db/timings");
    foreach($db as $line){
	if ($i==$timing_id){
        $data=explode ("**",$line);
	$timings=unserialize($data[1]);
	echo '<input type="text" name="name" value="'.$data[0].'" /><br /><br /><div id="parts">';
		
	}
	$i++;
	}
$j=1;
$k=0;
while($j<=50){
if (isset($timings['name'.$j])){
echo '<input id="add_part" type="button" value="Add a part here" onclick="javascript:addBefore('.$j.')" /><br /><b>Part '.$j.'</b>( <a href="javascript:removePart('.$j.')">X remove part '.$j.'</a> )<br />name : <input type="text" name="name'.$j.'" id="name'.$j.'" value="'.$timings['name'.$j].'" /><br />lenght : <input type="text" name="length'.$j.'" id="length'.$j.'" value="'.$timings['length'.$j].'" />min<br /><br />';
$k=$j;
}
$j++;
}
if($k!=0){
echo '
<script type="text/javascript">
id='.$k.';
</script>
';
}
?>
</div>
<input id="add_part" type="button" value="Add an other part" onclick="javascript:addPart()" /><br /><br />
<input type="hidden" name="timing_id" value="<?PHP echo $timing_id;?>" />
<a href="./timings">cancel</a> <input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP
}else{
?>
<div id="page">
<h2>Timing</h2>
Click <a href="./timings">here</a> to view the timings.<br /><br />
</div>
<?PHP
}
?>