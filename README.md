# api-docker 
  
## How to install

- git clone ...
- cd api-docker/src
- sudo docker-compose up

- open new terminal
- cd api-docker/src
- docker-compose exec app sh
- composer install 
- cp .env.example .env
- php artisan key:generate
- php artisan migrate
- php artisan db:seed


## How to use tests

- open new terminal
- cd api-docker/src
- php artisan test

## Best Practices 
- Service Layer Pattern [  Models, Controllers, Services, Repositories]
- Translations [ Portuguese and English ]
- Containerization with Docker 