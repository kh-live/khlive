<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

$selected_date=date("Y",time())."-".date("m",time());
$selected_cong=$_SESSION['cong'];
if(isset($_GET['cong'])){
$selected_cong=$_GET['cong'];
$_SESSION['selected_cong_l']=$selected_cong;
$selected_date=date("Y",time())."-".date("m",time());
}
if(isset($_GET['date'])){
$selected_date=$_GET['date'];
$_SESSION['selected_date_l']=$selected_date;
}
if(isset($_SESSION['selected_cong_l'])) $selected_cong=$_SESSION['selected_cong_l'];
if(isset($_SESSION['selected_date_l'])) $selected_date=$_SESSION['selected_date_l'];
?>
<div id="page">
<script>
function update_cong(url){
  window.location="./logs?cong=" + url;
}
function update_date(url){
  window.location="./logs?date=" + url;
}
</script>
<h2><?PHP echo $lng['logs']; ?></h2>

<?PHP
echo 'Date: <select id="date" onchange="javascript:update_date(this.value)">';
	$month=date("m",time());
	$year=date("Y",time());
	$i=0;
	while($i<=12){
	$month_tmp=$month-$i;
	$year_tmp=$year;
	if($month_tmp<=0){
	$month_tmp+=12;
	$year_tmp--;
	}
	if($month_tmp<=9) $month_tmp="0".$month_tmp;
	$date=$year_tmp.'-'.$month_tmp;
	$opt="";
	if ($selected_date==$date) $opt='selected="selected"';
	echo '<option value="'.$date.'" '.$opt.'>'.$month_tmp.'/'.$year_tmp.'</option>';
	$i++;
	}
	echo '</select><br /><br />';
	echo $lng['congregation'].': <select id="cong" onchange="javascript:update_cong(this.value)">
	<option value="all">...</option>';
	$congs=file('./db/cong');
	foreach($congs as $congreg){
	$cong=explode("**",$congreg);
	$opt="";
	if ($selected_cong==$cong[0]) $opt='selected="selected"';
	echo '<option value="'.$cong[0].'" '.$opt.'>'.$cong[0].'</option>';
	}
	echo '</select><br /><br />';
	if ($selected_cong=="all"){
	$selected_cong="";
	}else{
	$selected_cong=$selected_cong."-";
	}
	$file="db/logs-".strtolower($selected_cong).$selected_date;

	if (!file_exists($file)){
	echo 'There is no data for this month. Try another one...';
	}else{
$db=array_reverse(file($file));
	$nb_total=count($db);
	    if (isset($_GET['log'])){
$page_nb=$_GET['log'];
}else{
$page_nb=0;
}
$page=0;
	$step=200;
	while($page<=$nb_total){
	if ($page!=0) echo "--";
	if ($page_nb==$page){
	echo '<a href="./logs?log='.$page.'" style="text-decoration:underline;">'.$page.'</a>';
	}else{
	echo '<a href="./logs?log='.$page.'" >'.$page.'</a>';
	}
	$page=$page+$step;
	}
	echo '<table>';
	$i=0;
    foreach($db as $line){
    if($page_nb<=$i AND $i<$page_nb+$step){
        $data=explode ("**",$line);
	echo '<tr><td>'.date('H:i:s-d/m/Y',$data[0]).'</td><td>'.@$data[1].'</td><td>'.@$data[2].'</td><td>'.@$data[3].'</td><td>'.@$data[4].'</td><td>'.@$data[5].'</td><td>'.@$data[6].'</td></tr>';
	}
	$i++;
}
}
?>
</table>
</div>


