![Project Banner](https://raw.githubusercontent.com/tighten/laravelversions/main/banner.png)
# Laravel Versions

A canonical source of everything you need to know about Laravel versions' support history and future.

## API

Full list of all released versions here:

https://laravelversions.com/api/versions

```json
{
    "data": [
        {
            "major": 8,
            "latest_minor": 24,
            "latest_patch": 0,
            "latest": "8.24.0",
            "is_lts": false,
            "released_at": "2020-09-08T00:00:00.000000Z",
            "ends_bugfixes_at": "2021-04-21T00:00:00.000000Z",
            "ends_securityfixes_at": "2021-09-08T00:00:00.000000Z",
            "status": "active",
            "links": [
                {
                "type": "GET",
                "rel": "self",
                "href": "https://laravelversions.com/api/versions/8"
                },
                {
                "type": "GET",
                "rel": "latest",
                "href": "https://laravelversions.com/api/versions/8.24.0"
                }
            ]
        },
        {}
    ]
}
```

Test individual versions like this:

https://laravelversions.com/api/versions/8
https://laravelversions.com/api/versions/8.1
https://laravelversions.com/api/versions/8.1.0

If you pass a major version (e.g. `/8`) you'll only see information about that major version, just like its entry in the full list (as seen above).

If you request a minor/patch version (e.g. `/8.1` or `/8.1.0`) you'll see an additional section called `specific_version` that give you details about whether your provided release is the most up-to-date for its major. For example, this would be the output of requesting `/8.1.5` at a time when `8.24.0` was the most up-to-date release for Laravel 8:

```json
{
    "data": {
        "major": 8,
        "latest_minor": 24,
        "latest_patch": 0,
        "latest": "8.24.0",
        "is_lts": false,
        "released_at": "2020-09-08T00:00:00.000000Z",
        "ends_bugfixes_at": "2021-04-21T00:00:00.000000Z",
        "ends_securityfixes_at": "2021-09-08T00:00:00.000000Z",
        "status": "active",
        "specific_version": {
            "provided": "8.1.5",
            "needs_patch": true,
            "needs_major_upgrade": false
        },
        "links": [
            {
                "type": "GET",
                "rel": "major",
                "href": "https://laravelversions.com/api/versions/8"
            },
            {
                "type": "GET",
                "rel": "self",
                "href": "https://laravelversions.com/api/versions/8.1.5"
            },
            {
                "type": "GET",
                "rel": "latest",
                "href": "https://laravelversions.com/api/versions/8.24.0"
            }
        ]
    }
}
```

Potential statuses:

- "active": receiving bug and security fixes
- "security": only receiving security fixes
- "end-of-life": no longer receiving security or bug fixes


## Instructions for hosting/installing yourself
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

> Note: In order to make page caching work, you'll need to follow the installation instructions if you're installing this site on a production server. https://github.com/JosephSilber/page-cache
