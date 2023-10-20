
composer dump-autoload
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
# php artisan cache:clear
# php artisan route:cache
# a2enmod rewrite
# service apache2 restart
echo "All Good, Everything built for develop, Happy coding!"
exit

# php artisan migrate:fresh --seed
# composer dump-autoload
# php artisan cache:clear
# php artisan route:cache

# /usr/bin/supervisord -c /etc/supervisord.conf