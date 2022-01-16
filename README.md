# Forecast-app

## Installation

- Add `forecast.local.io` to host file
- Go to project `docker` folder and run `docker-compose up`
- Go into Docker container `docker exec -it forecast_app_webserver bash`
  - In project root (`/var/www/html`) run `composer install`
  - And run `php bin/console -n doctrine:migrations:migrate`
- Navigate to browser and open http://forecast.local.io:8888 or http://forecast.local.io:8888/?ip=8.8.8.8
