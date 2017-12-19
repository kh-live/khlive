<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}
?>
<html>
<head>
<title><?PHP echo $lng['signin'];?></title>
<link rel="icon" sizes="144x144" href="./img/logo-small.png">
<style type="text/css">
<?PHP
include "./style.css";
?>
</style>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
</head>
<body>
<div id="titles" >
<img id="title_img" src="./img/logo.png" alt="KH-Live! Streaming" />
</div>
<div id="login">
<a href="javascript:show_quick_login()">Quick Login</a> | Standard Login
<form action="./login" method="post">
<input class="field_login" type="text" name="login_user" placeholder="<?PHP echo $lng['user'];?>"><br />
<input class="field_login" type="password" name="login_password" placeholder="<?PHP echo $lng['password'];?>"><br />
<input id="input_login" type="submit" value="<?PHP echo $lng['signin'];?>">
</form>
</div>
<div id="quick_login">
Quick Login | <a href="javascript:show_normal_login()">Standard Login</a>
<ul id="pad">
<li class="pad_number">7</li>
<li class="pad_number">8</li>
<li class="pad_number">9</li>
<li class="pad_number">4</li>
<li class="pad_number">5</li>
<li class="pad_number">6</li>
<li class="pad_number">1</li>
<li class="pad_number">2</li>
<li class="pad_number">3</li>
<li class="pad_number">*</li>
<li class="pad_number">0</li>
<li class="pad_number" id="hash">#</li>
</ul>
</div>
<div id="hint">
<b>Hint : </b>click on "Standard Login" to connect with your username and password.
</div>
<div id="login-message"><?PHP echo $login_error;?></div>
<script>
function show_normal_login(){
document.getElementById("quick_login").style.display ="none";
document.getElementById("login").style.display ="block";
}
function show_quick_login(){
document.getElementById("quick_login").style.display ="block";
document.getElementById("login").style.display ="none";
}
var quick_pwd="";
function click_button(no){
if (no=="*"){
quick_pwd="";
}else if (no=="#"){
/*login*/
document.location="./login?qlog=" + quick_pwd;
}else{
quick_pwd=quick_pwd + no;
}
}
var link_ele = document.getElementsByClassName('pad_number');
for (var i = 0; i < link_ele.length; ++i) {
    var link = link_ele[i];
	link.onclick = function(){
	click_button(this.innerHTML);
	this.style.backgroundColor="rgba(0,0,0,0.4)";
	this.style.boxShadow="0 0 8px rgba(0, 0, 0, 0.7)";
	window.setTimeout("reset_no()", 200);
	}
	}
function reset_no(){
	var link_ele = document.getElementsByClassName('pad_number');
for (var i = 0; i < link_ele.length; ++i) {
    var link = link_ele[i];
    link.style.backgroundColor="";
    link.style.boxShadow="";
    }
	}
</script>
</body>
</html>