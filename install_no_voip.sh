#!/bin/sh
IP_ADDR="192.168.1.123"
IP_GW="192.168.1.1"
KH_GRP="asterisk"
KH_USER="asterisk"
APACHE_ROOT="/var/www/html/"

if [ $(id -u) != "0" ]
then
echo 'Please run the script as root! sudo ./install.sh'
else
echo 'configuring IP address to '$IP_ADDR
sed -i 's/iface eth0 inet manual/auto eth0\
iface eth0 inet static\
address '$IP_ADDR'\
netmask 255.255.255.0\
gateway '$IP_GW'\
dns-nameservers 8.8.8.8\
dns-nameservers 8.8.4.4/' /etc/network/interfaces
echo 'configuring time zone'
dpkg-reconfigure tzdata
echo 'Change your password'
passwd pi
echo 'updating app database'
apt-get update
echo 'installing required software'
#remember to say no when asked to configure icecast2
apt-get install screen wget nano tar dos2unix apache2 php5 libapache2-mod-php5 php5-mcrypt icecast2 ices2 ezstream lame unzip moc moc-ffmp* dnsutils git -y
echo 'adding group to run the servers : '$KH_GRP
groupadd $KH_GRP
echo 'adding user to run the servers : '$KH_USER
useradd $KH_USER -g $KH_GRP
echo 'configuring apache'
a2enmod rewrite
#a2dismod cgi
#a2dismod negotiation
#a2dismod autoindex
#a2dismod setenvif
#awk '{for(i=1;i<=NF;i++){if(x<3&&$i=="AllowOverride None"){x++;sub("AllowOverride None","AllowOverride all",$i)}}}1' /etc/apache2/sites-available/default
#sed -i 's/multiviews//' /etc/apache2/sites-available/default
#sed -i 's/indexes//' /etc/apache2/sites-available/default
sed -i 's:session.gc_maxlifetime = 1440:session.gc_maxlifetime = 7200:' /etc/php5/apache2/php.ini
sed -i 's:#Include conf-available/serve-cgi-bin.conf:<Directory '$APACHE_ROOT'>\
Options -MultiViews +FollowSymLinks\
AllowOverride all\
Order allow,deny\
allow from all\
</Directory>:' /etc/apache2/sites-available/000-default.conf
sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER='$KH_USER'/' /etc/apache2/envvars
sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP='$KH_GRP'/' /etc/apache2/envvars
chown ${KH_USER}:${KH_GRP} /var/lock/apache2/
chown ${KH_USER}:${KH_GRP} /var/lib/php5/
echo 'Downloading and installing kh-live software'
mkdir ${APACHE_ROOT}kh-live
(cd ${APACHE_ROOT}kh-live && wget http://kh-live.co.za/downloads/khlive_latest.tar)
(cd ${APACHE_ROOT}kh-live && tar -xvf khlive_latest.tar)
mv ${APACHE_ROOT}kh-live/index.tdl.php ${APACHE_ROOT}index.php
rm ${APACHE_ROOT}index.html
rm ${APACHE_ROOT}kh-live/khlive_latest.tar
if [ $APACHE_ROOT != "/var/www/" ]
then
sed -i "s:web_server_root='/var/www/':web_server_root='"$APACHE_ROOT"':" ${APACHE_ROOT}kh-live/db/config.php
fi
echo 'configuring icecast2'
rm /etc/icecast2/icecast.xml
(cd /etc/icecast2 && ln -s ${APACHE_ROOT}kh-live/config/icecast.xml)
sed -i 's/USERID=icecast2/USERID=root/' /etc/default/icecast2
sed -i 's/GROUPID=icecast/GROUPID=root/' /etc/default/icecast2
sed -i 's/ENABLE=false/ENABLE=true/' /etc/default/icecast2
chown -R ${KH_USER}:${KH_GRP} /var/log/icecast*
chown -R ${KH_USER}:${KH_GRP} /usr/share/icecast*
if [ $KH_USER != "asterisk" ]
then
sed -i "s: <user>asterisk<: <user>"$KH_USER"<:" /etc/icecast2/icecast.xml
fi
if [ $KH_GRP != "asterisk" ]
then
sed -i "s: <group>asterisk<: <group>"$KH_GRP"<:" /etc/icecast2/icecast.xml
fi
echo 'configuring cron'
dos2unix -q ${APACHE_ROOT}kh-live/config/*
dos2unix -q ${APACHE_ROOT}kh-live/*
dos2unix -q ${APACHE_ROOT}kh-live/db/*
sed -i 's:/var/www/kh-live/:'${APACHE_ROOT}'kh-live/:' ${APACHE_ROOT}kh-live/config/cron
cp ${APACHE_ROOT}kh-live/config/cron /etc/cron.d/khlive
read -r -p "Do you want to install samba shares? [y/N] " response
case $response in
    [yY][eE][sS]|[yY])
       echo 'Installing samba...'
	apt-get install samba samba-common-bin -y
	sed -i 's:#   wins support = no:wins support = yes:' /etc/samba/smb.conf
sed -i 's:;   write list = root, @lpadmin:[khsongs]\
comment= KH songs\
path='$APACHE_ROOT'kh-live/kh-songs/\
browseable=Yes\
writeable=Yes\
only guest=no\
create mask=0777\
directory mask=0777\
public=yes\
force user = '$KH_USER'\
[khdownloads]\
comment= KH downloads\
path='$APACHE_ROOT'kh-live/downloads/\
browseable=Yes\
writeable=Yes\
only guest=no\
create mask=0777\
directory mask=0777\
public=yes\
force user = '$KH_USER'\
[khrecordings]\
comment= KH recordings\
path='$APACHE_ROOT'kh-live/records/\
browseable=Yes\
writeable=Yes\
only guest=no\
create mask=0777\
directory mask=0777\
public=yes\
force user = '$KH_USER':' /etc/samba/smb.conf
        ;;
    *)
       echo 'skipping samba...'
        ;;
esac
echo 'Installing Original songs 1-135 :'
echo 'Note : you need to have enough free space on the disk.'
echo '(Did you expand filesystem?)'
read -r -p ". Do you want to install the original songs? [y/N] " response
case $response in
    [yY][eE][sS]|[yY]) 
    read -r -p "Download from [w]eb ( 625Mo ) or use [L]ocal file? [w/L] " response
case $response in
    [wW][eE][bB]|[wW]) 
       echo 'downloading original songs...'
       (cd ${APACHE_ROOT}kh-live/kh-songs && wget https://download-a.akamaihd.net/files/media_music/d5/iasn_E.m4a.zip)
        ;;
    *)
       echo 'skipping download...'
        ;;
esac
       (cd ${APACHE_ROOT}kh-live/kh-songs && unzip iasn_E.m4a.zip)
       rm ${APACHE_ROOT}kh-live/kh-songs/iasn_E.m4a.zip
       chown -R ${KH_USER}:${KH_GRP} ${APACHE_ROOT}kh-live/*
        ;;
    *)
       echo 'skipping original songs...'
        ;;
esac
echo 'Installing New songs 136-150 :'
read -r -p "Do you want to install the new songs? [y/N] " response
case $response in
    [yY][eE][sS]|[yY]) 
     read -r -p "Download from [w]eb ( 83Mo ) or use [L]ocal file? [w/L] " response
case $response in
    [wW][eE][bB]|[wW]) 
       echo 'downloading new songs...'
       (cd ${APACHE_ROOT}kh-live/kh-songs && wget https://download-a.akamaihd.net/files/media_music/3a/snnw_E.m4a.zip)
        ;;
    *)
       echo 'skipping download...'
        ;;
esac
	(cd ${APACHE_ROOT}kh-live/kh-songs && unzip snnw_E.m4a.zip)
       rm ${APACHE_ROOT}kh-live/kh-songs/snnw_E.m4a.zip
       (cd ${APACHE_ROOT}kh-live/kh-songs && mv snnw_E_136.m4a iasn_E_136.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_137.m4a iasn_E_137.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_138.m4a iasn_E_138.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_139.m4a iasn_E_139.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_140.m4a iasn_E_140.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_141.m4a iasn_E_141.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_142.m4a iasn_E_142.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_143.m4a iasn_E_143.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_144.m4a iasn_E_144.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_145.m4a iasn_E_145.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_146.m4a iasn_E_146.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_147.m4a iasn_E_147.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_148.m4a iasn_E_148.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_149.m4a iasn_E_149.m4a)
(cd ${APACHE_ROOT}kh-live/kh-songs &&  mv snnw_E_150.m4a iasn_E_150.m4a)
chown -R ${KH_USER}:${KH_GRP} ${APACHE_ROOT}kh-live/*
        ;;
    *)
       echo 'skipping new songs...'
        ;;
esac
echo 'Installing Orchestral songs 1-135 :'
read -r -p "Do you want to install the orchestral songs? [y/N] " response
case $response in
    [yY][eE][sS]|[yY]) 
        read -r -p "Download from [w]eb ( 317Mo ) or use [L]ocal file? [w/L] " response
case $response in
    [wW][eE][bB]|[wW]) 
       echo 'downloading orchestral songs...'
      (cd ${APACHE_ROOT}kh-live/kh-songs && wget https://download-a.akamaihd.net/files/media_music/d3/iasnm_E.m4a.zip)
        ;;
    *)
       echo 'skipping download...'
        ;;
esac
       (cd ${APACHE_ROOT}kh-live/kh-songs && unzip iasnm_E.m4a.zip)
       rm ${APACHE_ROOT}kh-live/kh-songs/iasnm_E.m4a.zip
       (cd ${APACHE_ROOT}kh-live/kh-songs && find . -type f -name "iasnm*.m4a" -exec bash -c 'mv "$0" "${0/iasnm/iasn}"' {} \;)
       chown -R ${KH_USER}:${KH_GRP} ${APACHE_ROOT}kh-live/*
        ;;
    *)
       echo 'skipping orchestral songs...'
        ;;
esac
echo 'Automatic updater installation'
mkdir /root/update_dir
(cd /root/update_dir && git clone https://github.com/kh-live/khlive.git)
sed -i 's:/var/www/kh-live/:'${APACHE_ROOT}'kh-live/:' /root/update_dir/khlive/update_script_debian.sh
sed -i 's/asterisk:asterisk/'$KH_USER':'$KH_GRP'/' /root/update_dir/khlive/update_script_debian.sh
(cd /root/update_dir/khlive && cp update_script_debian.sh update.sh)
chmod +x  /root/update_dir/khlive/update.sh
echo 'activating reboot function'
sed -i 's_#includedir /etc/sudoers.d_'$KH_USER' ALL=NOPASSWD: /sbin/reboot_'  /etc/sudoers
echo 'updating to latest version'
/root/update_dir/khlive/update.sh
echo 'cleaning up'
chown -R ${KH_USER}:${KH_GRP} /var/www*
read -r -p "Do you want to update the operating system? [y/N] " response
case $response in
    [yY][eE][sS]|[yY])
       echo 'Updating...'
	apt-get upgrade
        ;;
    *)
       echo 'skipping update...'
        ;;
esac
echo 'Restarting apache server'
service apache2 restart
echo 'Restarting icecast2 server'
service icecast2 restart
echo 'Installation finished.'
echo 'You need to reboot now to change your ip address.'
echo 'Your new IP will be : '$IP_ADDR
read -r -p "Do you want to reboot now? [y/N] " response
case $response in
    [yY][eE][sS]|[yY])
       echo 'rebooting...'
	reboot
        ;;
    *)
       echo 'skipping reboot...'
        ;;
esac

fi