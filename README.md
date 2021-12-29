# f3rva-api

## Installation
- Install PHP
- Install Composer
    composer update
- Add the following to php.ini

        [xdebug]
        xdebug.mode = debug,coverage
        xdebug.start_with_request = yes
        xdebug.client_port = 9000

## Commands

### Web Server
    php -S localhost:8000 -t public

### Code Coverage
    ./vendor/bin/phpunit --coverage-html html --coverage-filter src test