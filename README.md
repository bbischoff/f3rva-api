# f3rva-api

## Installation
- Install PHP
- Install Composer
    composer install
- Add the following to php.ini

        [xdebug]
        xdebug.mode = debug,coverage
        xdebug.start_with_request = yes
        xdebug.client_port = 9000

## Commands

### Web Server
    composer local

### Code Coverage
    composer test
