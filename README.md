![Project Banner](https://raw.githubusercontent.com/tighten/this-project/main/banner.png)
# Laravel Versions

A canonical source of everything you need to know about Laravel versions' support history and future.

### Requirements

* PHP >= 7.3 with [the extensions listed in the Laravel docs](https://laravel.com/docs/8.x/deployment#server-requirements)
* A [supported relational database](http://laravel.com/docs/8.x/database#introduction) and corresponding PHP extension
* [Composer](https://getcomposer.org/download/)
* [NPM](https://nodejs.org/)

### Installation

1. (Optionally) [Fork this repository](https://help.github.com/articles/fork-a-repo/)
1. Clone the repository locally
1. [Install dependencies](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies) with `composer install`
1. Copy `.env.example` to `.env` and modify its contents to reflect your local environment.
1. Generate an application key

    ```bash
    php artisan key:generate
    ```
1. Create a database and point the `DB_DATABASE` to it in the `.env` file.
1. [Run database migrations](http://laravel.com/docs/8.x/migrations#running-migrations). If you want to include seed data, add a `--seed` flag.

    ```bash
    php artisan migrate
    ```
1. [Install frontend dependencies](https://docs.npmjs.com/cli/install) with `npm install`
1. Build frontend assets with `npm run dev`
1. Configure a web server, such as the [built-in PHP web server](http://php.net/manual/en/features.commandline.webserver.php), to use the `public` directory as the document root.

    ```bash
    php -S localhost:8080 -t public
    ```
1. Run tests with `php artisan test`.
