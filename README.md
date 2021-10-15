# Olive ObserverSensorium API and WebApp

### System Requirements:

1. php ^7.4
2. MariaDB ^10 || MySQL ^8.0
3. composer
4. npm
5. node 12.14

## Initial Setup

Clone the project and go to its directory. Open a terminal in there and run the following commands:

1. `composer install`
2. `npm install`
3. `cp .env.example .env`
4. Fill in the following in the .env file according to the system you run:
   1. DB_DATABASE (with the name of a new database you create)
   2. DB_USERNAME (mysql username with full access and privilages to the database)
   3. DB_PASSWORD (mysql password for the user above)
   4. ADMIN_PASS (for login as admin to the web app)
5. `php artisan migrate:fresh --seed`
6. `php artisan key:generate`
7. `php artisan storage:link`
8. `php artisan passport:install`
9. `php artisan passport:keys`
10. `php artisan passport:client --personal`
11. `php artisan l5-swagger:generate`

## Development Setup

Run in terminal the following commands:

1. `php artisan serve`
2. `npm run watch`

## Docker Setup

Run the following commands:

1. `php artisan sail:install`
   * Do not forget to make the nessecary changes to the .env file afterwards
2. `./vendor/bin/sail up -d`

Then Run:

1. `./vendor/bin/sail artisan migrate:fresh --seed`
2. `./vendor/bin/sail artisan key:generate`
3. `./vendor/bin/sail artisan storage:link`
4. `./vendor/bin/sail artisan passport:install`
5. `./vendor/bin/sail artisan passport:client --personal`
6. `./vendor/bin/sail artisan l5-swagger:generate`
7. `./vendor/bin/sail npm run prod`
