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
sed -i 's/#interface eth0/interface eth0\
static ip_address='$IP_ADDR'\
static routers='$IP_GW'\
static domain_name_servers=8.8.8.8/' /etc/network/interfaces
echo 'configuring time zone'
dpkg-reconfigure tzdata
echo 'Change your password'
passwd pi
echo 'updating app database'
apt-get update
echo 'installing required software'
#remember to say no when asked to configure icecast2
apt-get install screen wget nano tar dos2unix apache2 php7.0 libapache2-mod-php7.0 php7.0-mcrypt php7.0-curl php7.0-zip alsa-base icecast2 ices2 ezstream lame unzip moc moc-ffmp* dnsutils git usbmount -y
mkdir /home/${KH_USER}
mkdir /home/${KH_USER}/.moc
chown ${KH_USER}:${KH_GRP} /home/${KH_USER}
chown ${KH_USER}:${KH_GRP} /home/${KH_USER}/.moc
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
sed -i 's:session.gc_maxlifetime = 1440:session.gc_maxlifetime = 7200:' /etc/php/7.0/apache2/php.ini
sed -i 's:#Include conf-available/serve-cgi-bin.conf:<Directory '$APACHE_ROOT'>\
Options -MultiViews +FollowSymLinks\
AllowOverride all\
Order allow,deny\
allow from all\
</Directory>:' /etc/apache2/sites-available/000-default.conf
sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER='$KH_USER'/' /etc/apache2/envvars
sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP='$KH_GRP'/' /etc/apache2/envvars
chown ${KH_USER}:${KH_GRP} /var/lock/apache2/
chown -R ${KH_USER}:${KH_GRP} /var/lib/php*

echo 'Downloading and installing kh-live software'
mkdir ${APACHE_ROOT}kh-live
(cd ${APACHE_ROOT}kh-live && wget http://kh-live.co.za/downloads/khlive_latest.tar)
(cd ${APACHE_ROOT}kh-live && tar -xvf khlive_latest.tar)
mv ${APACHE_ROOT}kh-live/index.tdl.php ${APACHE_ROOT}index.php
rm ${APACHE_ROOT}index.html
rm ${APACHE_ROOT}kh-live/khlive_latest.tar
sed -i 's:/var/www/kh-live/:'${APACHE_ROOT}'kh-live/:' ${APACHE_ROOT}kh-live/config/update.sh
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
writeable=No\
only guest=no\
create mask=0777\
directory mask=0777\
public=yes\
force user = '$KH_USER'\
[khdownloads]\
comment= KH downloads\
path='$APACHE_ROOT'kh-live/downloads/\
browseable=Yes\
writeable=No\
only guest=no\
create mask=0777\
directory mask=0777\
public=yes\
force user = '$KH_USER'\
[khrecordings]\
comment= KH recordings\
path='$APACHE_ROOT'kh-live/records/\
browseable=Yes\
writeable = No\
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

echo 'Automatic update configuration'
#mkdir /root/update_dir
git config --global http.postbuffer "10m"
#(cd /root/update_dir && git clone https://github.com/kh-live/khlive.git)
#sed -i 's:/var/www/kh-live/:'${APACHE_ROOT}'kh-live/:' /root/update_dir/khlive/update_script_debian.sh
sed -i 's/asterisk:asterisk/'$KH_USER':'$KH_GRP'/' ${APACHE_ROOT}kh-live/db/config.php
#(cd /root/update_dir/khlive && cp update_script_debian.sh update.sh)
#chmod +x  /root/update_dir/khlive/update.sh
echo 'activating reboot function'
sed -i 's_#includedir /etc/sudoers.d_'$KH_USER' ALL=NOPASSWD: /sbin/reboot_'  /etc/sudoers
#echo 'updating to latest version'
#/root/update_dir/khlive/update.sh
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
echo 'If you have a USB sound card. Plug it in now. make sure it is connected to some speakers. We will do a sound check once it is installed.'
read -r -p "Do you want to install the USB sound card support now? [y/N] " response
case $response in
    [yY][eE][sS]|[yY])
       echo 'Installing sound card...'
usermod -a -G audio ${KH_USER}
mkdir /home/${KH_USER}
chown ${KH_USER}:${KH_GRP} /home/${KH_USER}
cp ${APACHE_ROOT}kh-live/asound.conf /etc/asound.conf
 cp /etc/asound.conf /home/${KH_USER}/.asoundrc
 chown ${KH_USER}:${KH_GRP} /home/${KH_USER}/.asoundrc
echo 'Testing sound card...'
speaker-test -c 2 -l 5
alsamixer
        ;;
    *)
       echo 'skipping sound card...'
        ;;
esac
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