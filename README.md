
1. composer install
2. .env = DATABASE_URL="mysql://root:password@127.0.0.1:3307/app?serverVersion=mariadb-10.4.32"
3. php bin/console doctrine:database:create
4. php bin/console doctrine:migrations:migrate
5. php bin/console doctrine:fixtures:load
6. symfony serve
