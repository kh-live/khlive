<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include '404.php';
exit(); 
}
include 'functions.php';
if(isset($_POST['submit'])){
	if($_POST['submit']==$lng['save']){
	$timings=array();
		if ($_POST['name']!=""){
		//we obviously need to check the input
			$name=$_POST['name']; //check
			foreach ($_POST as $key=>$value){
			if ($key!="submit" AND $key!="name"){
			$timings[$key]=$value;
			}
			}
			
$adding=timing_add($name,$timings);
if ($adding=='ok'){
echo '<div id="ok_msg">'.$lng['op_ok'].'...</div>';
$info=time().'**info**new timing add successful**'.$name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}else{
echo $adding;
$info=time().'**error**new timing add fail**'.$name."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
	}
}
		}else{
		echo '<div id="error_msg">'.$lng['fill_incorrect'].'...</div>';
		}
	} ?>
	<div id="page">
<h2>Timing</h2>
Click <a href="./timings">here</a> to view the timings.<br /><br />
</div>
<?PHP
}else{
?>
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
<div id="page">
<h2>Timing</h2>
Add new timing<br /><br />
<form action="./timing_add" method="post">
<b>Timing name</b><br />
<input type="text" name="name" /><br /><br />

<div id="parts"><input id="add_part" type="button" value="Add a part here" onclick="javascript:addBefore(1)" /><br /><b>Part 1</b>( <a href="javascript:removePart(1)">X remove part 1</a> )<br />
name : <input type="text" id="name1" name="name1" /><br /> length : <input type="text" id="length1" name="length1" />min<br /><br /></div>
<input id="add_part" type="button" value="Add an other part" onclick="javascript:addPart()" /><br /><br />
<a href="./timings">cancel</a> <input name="submit" type="submit" value="<?PHP echo $lng['save'];?>" />
</form>
</div>
<?PHP } ?>