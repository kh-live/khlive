#!/bin/sh
#Git update and apply script
git pull
cp /root/update_dir/khlive/*.php /var/www/kh-live/
cp /root/update_dir/khlive/*.css /var/www/kh-live/
cp /root/update_dir/khlive/.htaccess /var/www/kh-live/
chown -R asterisk:asterisk /var/www/kh-live/*
echo "done"
