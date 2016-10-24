#!/bin/sh
#Git update and apply script
(cd /root/update_dir/khlive && git pull)
cp /root/update_dir/khlive/*.php /var/www/kh-live/
cp /root/update_dir/khlive/*.css /var/www/kh-live/
cp /root/update_dir/khlive/.htaccess /var/www/kh-live/
dos2unix /var/www/kh-live/* > /dev/null 2>&1
find /var/www/kh-live/ -type f -printf '"%p"\n' | xargs chmod 640 
chown -R asterisk:asterisk /var/www/kh-live/*
(cd /root/update_dir/khlive && cp update_script_debian.sh update.sh)
(cd /root/update_dir/khlive && chmod 700 update.sh)
chmod +x /var/www/kh-live/config/update.sh
chmod +x /var/www/kh-live/config/downloader.sh
echo "done"
