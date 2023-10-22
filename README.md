# Assignment

## Requirements

- PHP: 8.1+
- MySQL: 5.7+

## Prerequisites

1. Setup database
2. Create database account

# Setup

## Automatic Setup

1. `composer install`
2. `php bin/console setup`
3. update `config/env.php` file
4. run server `php -S localhost:8080 -t public`
5. browse http://127.0.0.1:8080/

## Manual setup

1. `composer install`
2. copy `env.example.php` to `env.php` and modify
3. import `resources/schema/schema.sql` to database
4. run server `php -S localhost:8080 -t public`
5. browse http://127.0.0.1:8080/

## Docker
`docker compose up`


# Testing
`composer test`

# Examples

## hoppscotch

Is an API tester similar to PostMan. You can import the examples collection by browsing to https://hoppscotch.io/ and
importing `resources/hoppscotch.json`

## open-api UI

1. run server ``
2. `npm i -g open-swagger-ui`
3. cd `resources/api`
4. `open-swagger-ui ./api.yaml --open`