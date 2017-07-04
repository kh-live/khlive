<html>
<head>
<link rel="manifest" href="/manifest.php">
</head>
<body style="background-color:black;">
<script type="text/javascript">
function refreshPage(){
window.location='./time';
}
</script>
<style type="text/css">
@font-face{
	font-family: test1;
	src:url('./fonts/digital-7-mono.ttf');
}
@font-face{
	font-family: test2;
	src:url('./fonts/Orbitron-Black.ttf');
}
#meeting_time{
	font-family: 'test2', sans-serif;
	position:absolute;
	top:40px;
	left:0;
	width:100%;
	background-color: black;
	margin:0;
	color:white;
}
#meeting_overall{
	background-color:black;
	width:100%;
	padding-top:30px;
	padding-bottom:30px;
	text-align:center;
	font-size:2em;
}
#meeting_times, #meeting_clock{
	font-family: 'test1', sans-serif;
	text-align:center;
	font-size:5em;
}
#hours, #minutes, #secondes, #meeting_clock h1{
display:inline-block;
}
 #meeting_clock{
 color:rgba(255,255,255,0.05);
position:absolute;
width:100%;
 }
</style>
<?PHP
	include ("./meeting-time.php");
echo '</body>
	</html>';
die();
?>