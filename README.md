## KoDram

A web application where users can view the details related to their favorite Korean Drama and Korean Movies.

The details are fetched from the [The Movie Database](https://themoviedb.org).
Therefore, you will have to get the API Key and the Read Access Token after registering on their website.

### Installation

```bash
git clone git@github.com:MehulBawadia/kodram.git
cd kodram
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve --host=localhost
npm run dev
```

### License

This project is an open-sourced software licensed under the [MIT License](https://opensource.org/license/mit)
