## LARAVEL 8 VILLA RENTAL PLATFORM API
#### !!! Project Under Development

### PROJECT DESCRIPTION
A backend API for Villa Rental Platform. The platform will allow users to create accounts as distributors or as providers. Registered providers can upload villas to be rented, manage their prices, booking policies and availabilities. Registered distributors can consume data in reseller capacity, incorporating received data to their own websites or apps with posibility to make bookings. 
##### Note: Only part if this project is available in public repository.

### INSTALLATION & CONFIGURATION

#### CLONE THE PROJECT
#### INSTALL COMPOSER DEPENDENCIES
```bash
composer install
```
#### CREATE A .env FILE. 
Use as example the existing .env.example file. It includes all environment variables that are needed to be set for this specific app to function along with some recommended  default values.
#### SETUP DATABASE
  *  Create mysql database 
  *  Set the database credentials in .env file
```code
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
  * Create database tables.
  ```bash
  php artisan migrate
  ```
  If fake data is needed, seed the database.
  ```bash
  php artisan db:seed 
  ```

### MAIN PLUGINS AND LIBRARIES IN PROJECT

* Authentication [Laravel Sanctum](https://github.com/laravel/sanctum)
* PHP image handling and manipulation library [Intervention Image](http://image.intervention.io/)

