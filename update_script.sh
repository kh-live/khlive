#!/bin/sh
#Git update and apply script
git pull
cp /root/update_dir/khlive/*.php /var/www/html/kh-live/
cp /root/update_dir/khlive/*.css /var/www/html/kh-live/
cp /root/update_dir/khlive/.htaccess /var/www/html/kh-live/
chown -R asterisk:asterisk /var/www/html/kh-live/*
echo "done"
