if ! [ -e ./vendor ]
then
    composer install
fi

php bin/console make:migration && php bin/console d:m:m

php-fpm


