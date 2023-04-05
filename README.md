# Test app
### Setup
1. Clone this repository
2. Run ```composer install```
3. Run ```cp .env.example .env```
4. Run docker as background deamon ```./vendor/bin/sail up -d```
5. Generate token key ```./vendor/bin/sail bash``` && ```openssl genrsa -out token-key.pem 2048```
6. Generate laravel key ```./vendor/bin/sail php artisan key:generate```
7. Run laravel migrations ```./vendor/bin/sail php artisan migrate:refresh --seed```
8. Generate swagger documentation ```./vendor/bin/sail php artisan l5-swagger:generate```
