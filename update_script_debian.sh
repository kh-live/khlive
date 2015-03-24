#!/bin/sh
#Git update and apply script
git pull
cp /root/update_dir/khlive/*.php /var/www/kh-live/
cp /root/update_dir/khlive/*.css /var/www/kh-live/
cp /root/update_dir/khlive/.htaccess /var/www/kh-live/
dos2unix /var/www/kh-live/* > /dev/null 2>&1
chmod -R 640 /var/www/kh-live/*
chown -R asterisk:asterisk /var/www/kh-live/*
cp update_script_debian.sh update.sh
chmod 700 update.sh
echo "done"
