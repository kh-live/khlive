<?PHP
if(isset($_GET['autoup'])){
	if($_GET['autoup']=='ok'){
set_time_limit(300);
if (is_file('./db/config.php')){
include './db/config.php';
}else{
	//we are running from cli after a fresh install
	$version='2.3.3-fresh';
	$web_server_root='/var/www/html/';
	$temp_dir='/dev/shm/';
}
$version_start=$version;
$auto_update_dir=$temp_dir.'kh_auto_update';
$auto_update_dir_git=$auto_update_dir.'/khlive';
/*update goes here*/
if (!is_dir($auto_update_dir)){
mkdir($auto_update_dir);
exec('cd '.$auto_update_dir.' && git clone https://github.com/kh-live/khlive.git');
}
ob_start();
?>#!/bin/sh
#Git update and apply script
(cd <?PHP echo $auto_update_dir_git; ?> && git pull)
mkdir -p <?PHP echo $web_server_root; ?>kh-live/fonts
cp <?PHP echo $auto_update_dir_git; ?>/*.php <?PHP echo $web_server_root; ?>kh-live/
cp <?PHP echo $auto_update_dir_git; ?>/*.css <?PHP echo $web_server_root; ?>kh-live/
cp <?PHP echo $auto_update_dir_git; ?>/.htaccess <?PHP echo $web_server_root; ?>kh-live/
cp <?PHP echo $auto_update_dir_git; ?>/fonts/*.ttf <?PHP echo $web_server_root; ?>kh-live/fonts
cp <?PHP echo $auto_update_dir_git; ?>/img/*.png <?PHP echo $web_server_root; ?>kh-live/img
dos2unix <?PHP echo $web_server_root; ?>kh-live/* > /dev/null 2>&1
find <?PHP echo $web_server_root; ?>kh-live/ -type f -printf '"%p"\n' | xargs chmod 640 
chown -R asterisk:asterisk <?PHP echo $web_server_root; ?>kh-live/*
chown -R asterisk:asterisk /var/log/icecast2
chmod +x <?PHP echo $web_server_root; ?>kh-live/config/update.sh
chmod +x <?PHP echo $web_server_root; ?>kh-live/config/downloader.sh
echo "done"
<?PHP
$info= ob_get_clean();
	$file=fopen($web_server_root.'kh-live/auto_update.sh','w');
			if(fputs($file,$info)){
			fclose($file);
			}
//the chmod is replaced by the find xarg chmod command in the script as a safety
chmod($web_server_root.'kh-live/auto_update.sh', 0750);
exec($web_server_root.'kh-live/auto_update.sh',$return);


	$tmp_file=file_get_contents($web_server_root.'kh-live/config.php');
$tmp_file2=explode('\';//gen_version', $tmp_file);
$tmp_file3=explode('$gen_version=\'',$tmp_file2[0]);
$version_end=$tmp_file3[1];

$info=time().'**info**auto updated '.$version_start.' to '.$version_end.'**'.serialize($return)."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
echo '<div id="page"><h2>Server Update</h2>updated from '.$version_start.' to '.$version_end.' ! <br /> Now go to configuration page. 
<br /> 1. Check the settings and click <b>save</b> to apply the update.<br /> 2. Go back to configuration page and click <b>over-write config!</b> to reload the configuration.</div>';
}
}else{
echo '<div id="page"><h2>Server Update</h2><a href="./auto_update?autoup=ok">click here to update</a></div>';
}
?>