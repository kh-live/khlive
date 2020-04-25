<script>
function submitMultiUser(){
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    resp=xmlhttp.responseText;
    if (resp=="ok"){
//everything went fine
    }else{
	alert("There was an error while updating the name. Please try again.");
    }
   
    }
  }
  var newName=document.getElementById("multi-name").value
xmlhttp.open("GET","listener_joined.php?action=update_name&user=<?PHP echo $_SESSION['user'];?>&khuid=<?PHP echo $_SESSION['khuid'];?>&cong=<?PHP echo $_SESSION['cong'];?>&newname=" + newName, true);
xmlhttp.send();

}
</script>
<div id="multiusers">
Since you are using a generic account, please let us know your name :<br /><br />
<?PHP
$newname="";
if (isset($_SESSION['new_name'])) $newname=$_SESSION['new_name'];
?>
<input id="multi-name" type="text" value="<?PHP echo $newname; ?>" style="width:240px;padding:10px;" placeholder="type your name here, then press save ->" /><input type="button" value="Save" onclick="javascript:submitMultiUser()"/>
</div>