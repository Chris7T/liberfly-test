
# Liberfly Api


API for Testing


# About the API

### The api was made using the PHP language and the Laravel framework:

 - Creation of an `Action` layer to separate the business rule from the application.
 - Creation of a layer of `Repositories` to abstract the use of the database.
 - Creation of `Unit Tests` to predict the functionality of an Actions.
 - Creation of `Functional Tests` to predict the communication between actions and repositories.
 - Creation of a `Swagger` for documentation.
 - Authentication creation using JWT

# Run project

## Requirements

- Docker
- Docker-compose

## Step by step

1. - Clone the project
```
git clone https://github.com/Chris7T/liberfly-test.git
```
2. - Enter the project folder
```
cd liberfly-test
```
3. - Up the containers
```
docker-compose up -d
```
4. - Enter the workspace
```
docker exec -it app bash
```
5. - Install the composer
```
composer i
```
6. - Generate .env
```
cp .env.example .env
```
7. - Generate the API Key
```
php artisan key:generate
```
8. - Generate the JWT secret
```
php artisan jwt:secret
```
9. - Run the migrations
```
php artisan migrate
```
10. - Run the tests
```
php artisan test
```
11. - Generate documentation
```
php artisan l5-swagger:generate
```


# Documentation link

Para acessar a documentação basta acessar o link 

```
   /api/documentation/
```
