<?PHP
$test=$_SERVER['REQUEST_URI'];
if (strstr($test, ".php")){
header("HTTP/1.1 404 Not Found");
include "404.php";
exit(); 
}

if ($lang=="fr"){

}else{
$lng['PIN']='PIN';
$lng['welcome_instructions']='You may listen to the meeting in three different ways :<br />1.Click on the link "listen" above.<br />2.Call the no :044 004 0044 from your phone.<br />3.Connect to the meeting with a VOIP client simply call "meeting@kh.sinux.ch" . ';
$lng['listening']='Listen';
$lng['listening_text']='The meeting should start automatically. If it doesn\'t, try to press the "play" button.<br /> Note that there is a delay of at least 30sec with the actual meeting.';
$lng['user']='User';
$lng['meeting']='Meeting';
$lng['password']='Password';
$lng['signin']='Sign in';
$lng['home']='Home';
$lng['admin']='Admin';
$lng['logout']='Logout';
$lng['serverko']='server offline';
$lng['serverok']='server online';
$lng['nolive']='There doesn\'t seem to be any meeting happening at this time. Please wait until the meeting starts';
$lng['click2listen']='Alternative link';
$lng['alern_link']='Your navigator is not compatible with this feed.<br /> Try this link as an alternative (may work with vlc or wmp plugins):';
$lng['notstream']='Not available';
$lng['noconnection']='The KH is not reachable. Try again latter!';
$lng['contactadmin']='If the problem lasts please contact the administrator.';
$lng['badlogin']='Bad login. Try again!';
$lng['users']='Users';
$lng['streams']='Streams';
$lng['stream']='Stream';
$lng['error']='Error!';
$lng['welcome']='Welcome';
$lng['cong_part']='You\'re part of the congregation';
$lng['yeslive']='It seems your congregation is having a meeting right now. Use the player below to listen in';
$lng['listen_records']='Alternatively you can listen to recorded meetings by clicking on "Recordings" link above';
$lng['nofeed_setup']='Your congregation doesn\'t have any feeds setup. Please come back once it has been done.';
$lng['recordings']='Recordings';
$lng['congregations']='Congregations';
$lng['name']='Name';
$lng['congregation']='Congregation';
$lng['actions']='Actions';
$lng['edit']='Edit';
$lng['delete']='Delete';
$lng['rights']='Rights';
$lng['file']='File';
$lng['size']='Size';
$lng['download']='Download';
$lng['file_transfer']='File Transfer';
$lng['add_new_stream']='Add a new stream';
$lng['save']='Save';
$lng['select']='Select';
$lng['fill_incorrect']='Please fill the form correctly';
$lng['op_ok']='Operation succesful';
$lng['remove_stream']='Are you sure you want to remove this stream?';
$lng['cancel']='Cancel';
$lng['edit_stream']='Use the form below to edit the stream.';
$lng['add_new_congregation']='Add a new congregation';
$lng['name_exists']='This name already exists. Please choose an other one.';
$lng['remove_cong']='Are you sure you want to remove this congregation?';
$lng['add_new_user']='Add a new user';
$lng['user_admin']='Administrator';
$lng['user_root']='Administrator';
$lng['user_manager']='Manager';
$lng['user_user']='User';
$lng['remove_user']='Are you sure you want to remove this user?';
$lng['edit_user']='Use the form below to edit the user.';
$lng['type']='Type';
$lng['not_available']='Not available';
$lng['live_streams']='Live Streams';
$lng['live_streams_txt']='List of all the streams announced to this server (even the ones that may not be configured)';
$lng['date_started']='Date started';
$lng['no_live_streams']='No live streams';
$lng['logs']='Logs';
$lng['live_users']='Listeners';
$lng['live_users_txt']='List of all the listeners connected right now';
$lng['no_live_users']='No current listeners';
$lng['cong_id']='Congregation ID';
$lng['admin_pin']='Admin PIN';
$lng['report']='Report';
$lng['status']='Status';
}
?>
