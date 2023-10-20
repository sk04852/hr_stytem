cd /usr/share/nginx/posweb.digibits.xyz/backend
git checkout develop
git pull
composer install
php artisan migrate:fresh --seed
echo "All Good, Everything built for develop, Happy coding!"
exit
