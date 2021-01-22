#!/bin/bash

cp .env.example .env
composer install
npm install
npm run dev
php artisan migrate --seed
