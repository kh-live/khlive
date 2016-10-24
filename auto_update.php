<?PHP
if(isset($_GET['autoup'])){
	if($_GET['autoup']=='ok'){
set_time_limit(300);
include './db/config.php';
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
cp <?PHP echo $auto_update_dir_git; ?>/*.php <?PHP echo $web_server_root; ?>/kh-live/
cp <?PHP echo $auto_update_dir_git; ?>/*.css <?PHP echo $web_server_root; ?>/kh-live/
cp <?PHP echo $auto_update_dir_git; ?>/.htaccess <?PHP echo $web_server_root; ?>/kh-live/
dos2unix <?PHP echo $web_server_root; ?>/kh-live/* > /dev/null 2>&1
find <?PHP echo $web_server_root; ?>/kh-live/ -type f -printf '"%p"\n' | xargs chmod 640 
chown -R asterisk:asterisk <?PHP echo $web_server_root; ?>/kh-live/*
(cd <?PHP echo $auto_update_dir_git; ?> && cp update_script_debian.sh update.sh)
(cd <?PHP echo $auto_update_dir_git; ?> && chmod 700 update.sh)
chmod +x <?PHP echo $web_server_root; ?>/kh-live/config/update.sh
chmod +x <?PHP echo $web_server_root; ?>/kh-live/config/downloader.sh
echo "done"
<?PHP
$info= ob_get_clean();
	$file=fopen($auto_update_dir_git.'/update.sh','w');
			if(fputs($file,$info)){
			fclose($file);
			}
chmod($auto_update_dir_git.'/update.sh', 0750);
exec($auto_update_dir_git.'/update.sh',$return);

$tmp_file=file_get_contents('./config.php');
$tmp_file2=explode('\';//gen_version', $tmp_file);
$tmp_file3=explode('$gen_version=\'',$tmp_file2[0]);
$version_end=$tmp_file3[1];
$info=time().'**info**auto updated '.$version_start.' to '.$version_end.'**'.serialize($return)."**\n";
	$file=fopen('./db/logs-'.date("Y",time()).'-'.date("m",time()),'a');
			if(fputs($file,$info)){
			fclose($file);
			}
echo '<div id="page"><h2>Server Update</h2>updated from '.$version_start.' to '.$version_end.' ! </div>';
}
}else{
echo '<div id="page"><h2>Server Update</h2><a href="./auto_update?autoup=ok">click here to update</a></div>';
}
?>