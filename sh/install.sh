#git clone https://github.com/Puzzlout/TrekToursFlyer.git
#cd TrekToursFlyer
composer install
composer update
echo "require a mysql root user with no password"
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update --force
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
php bin/console assetic:dump --env=prod --no-debug
php bin/console asset:install --symlink
php bin/console asset:install --symlink