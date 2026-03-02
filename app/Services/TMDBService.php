<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TMDBService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->apiKey = config('services.tmdb.api_key');
    }

    protected function request(string $endpoint, array $params = [])
    {
        $overridenParams = array_merge([
            'api_key' => $this->apiKey,
        ], $params);

        return Cache::remember(
            'tmdb_'.md5($endpoint.serialize($overridenParams)),
            now()->addHour(),
            function () use ($endpoint, $overridenParams) {
                return Http::retry(3, 1000)
                    ->timeout(15)
                    ->withOptions([
                        'http_version' => CURL_HTTP_VERSION_1_1,
                        'connect_timeout' => 5,
                    ])
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => config('app.name').'/1.0',
                    ])
                    ->get($this->baseUrl.$endpoint, $overridenParams)
                    ->json();
            }
        );
    }

    public function getPopularTvSeries()
    {
        return Cache::remember('popular_tv_series', now()->addHour(), function () {
            return $this->request('/discover/tv', [
                'air_date.lte' => today()->toDateString(),
                'with_original_language' => 'ko',
                'watch_region' => 'KR',
                'sort_by' => 'popularity.desc',
                'with_runtime.gte' => 30,
                'vote_count.gte' => 50,
                'without_genres' => $this->getUnwantedTvGenres(),
            ]);
        });
    }

    public function getPopularMovies()
    {
        return Cache::remember('popular_movies', now()->addHour(), function () {
            return $this->request('/discover/movie', [
                'air_date.lte' => today()->toDateString(),
                'with_original_language' => 'ko',
                'watch_region' => 'KR',
                'sort_by' => 'popularity.desc',
                'vote_average.lte' => 10,
                'without_genres' => $this->getUnwantedMovieGenres(),
                'without_keywords' => '155477',
            ]);
        });
    }

    public function getSlidersForHomePage()
    {
        return Cache::remember('sliders_home_page', now()->addHour(), function () {
            $dramas = $this->getPopularTvSeries()['results'] ?? [];
            $series = collect($dramas)
                ->sortByDesc('vote_average')
                ->take(5);

            $movies = $this->getPopularMovies()['results'] ?? [];
            $movies = collect($movies)
                ->sortByDesc('vote_average')
                ->take(5);

            $combined = $series->map(function ($item) {
                return [
                    ...$item,
                    'type' => 'tv',
                ];
            })->merge($movies->map(function ($item) {
                return [
                    ...$item,
                    'type' => 'movie',
                ];
            }));

            return $combined
                ->filter(function ($item) {
                    return ! empty($item['backdrop_path']) || ! empty($item['poster_path']);
                })
                ->shuffle()
                ->take(4)
                ->values()
                ->toArray();
        });
    }

    public function discoverDramas($year = null, $genres = [], $sort = 'popularity.desc', $page = 1)
    {
        $query = [
            'air_date.lte' => today()->toDateString(),
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => $sort,
            'vote_average.gte' => 3,
            'without_genres' => $this->getUnwantedTvGenres(),
            'page' => $page,
            'with_type' => '2|4', // 2 = miniseries, 4 = Scripted
            'with_status' => '0|1|2|3', // 0 = OnGoing, 1 = Upcoming, 2 = In Production, 3 = Ended
        ];

        if ($year) {
            $query['first_air_date_year'] = $year;
        }

        if (! empty($genres)) {
            $genres = array_map('intval', $genres);
            sort($genres);
            $query['with_genres'] = implode('|', $genres);
        }

        $cacheKey = md5(json_encode([
            'year' => $year,
            'genres' => $genres,
            'sort' => $sort,
            'page' => $page,
        ]));

        return Cache::remember("filtered_dramas_{$cacheKey}", now()->addHour(), function () use ($query) {
            return $this->request('/discover/tv', $query);
        });
    }

    public function getDramaDetails($id)
    {
        return Cache::remember("drama_details_{$id}", now()->addDay(), function () use ($id) {
            return $this->request("/tv/{$id}", [
                'append_to_response' => 'credits,images,similar',
            ]);
        });
    }

    public function getDramaSeasonDetails($id, $seasonNumber)
    {
        return Cache::remember("drama_season_details_{$id}_{$seasonNumber}", now()->addDay(), function () use ($id, $seasonNumber) {
            return $this->request("/tv/{$id}/season/{$seasonNumber}");
        });
    }

    public function discoverMovies($year = null, $genres = [], $sort = 'popularity.desc', $page = 1)
    {
        $query = [
            'primary_release_date.lte' => today()->toDateString(),
            'primary_release_date.gte' => '2000-01-01',
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => $sort,
            'vote_average.gte' => 3,
            'with_runtime.gte' => 30,
            'vote_count.gte' => 50,
            'without_genres' => $this->getUnwantedMovieGenres(),
            'page' => $page,
            'without_keywords' => '155477',
        ];

        if ($year) {
            $query['primary_release_year'] = $year;
        }

        if (! empty($genres)) {
            $genres = array_map('intval', $genres);
            sort($genres);
            $query['with_genres'] = implode('|', $genres);
        }

        $cacheKey = md5(json_encode([
            'year' => $year,
            'genres' => $genres,
            'sort' => $sort,
            'page' => $page,
        ]));

        return Cache::remember("filtered_movies_{$cacheKey}", now()->addHour(), function () use ($query) {
            return $this->request('/discover/movie', $query);
        });
    }

    public function getMovieDetails($id)
    {
        cache()->forget("movie_details_{$id}");
        return Cache::remember("movie_details_{$id}", now()->addDay(), function () use ($id) {
            return $this->request("/movie/{$id}", [
                'append_to_response' => 'credits',
            ]);
        });
    }

    public function getTvGenres()
    {
        return Cache::remember('tv_genres', now()->addDay(), function () {
            $list = $this->request('/genre/tv/list');
            $genres = collect($list['genres'] ?? []);

            $unwantedGenreIds = explode(',', $this->getUnwantedTvGenres());

            return $genres
                ->filter(function ($genre) use ($unwantedGenreIds) {
                    return ! in_array($genre['id'], $unwantedGenreIds);
                });
        });
    }

    public function getMoviesGenres()
    {
        return Cache::remember('movie_genres', now()->addDay(), function () {
            $list = $this->request('/genre/movie/list');
            $genres = collect($list['genres'] ?? []);

            $unwantedGenreIds = explode(',', $this->getUnwantedMovieGenres());

            return $genres
                ->filter(function ($genre) use ($unwantedGenreIds) {
                    return ! in_array($genre['id'], $unwantedGenreIds);
                });
        });
    }

    protected function getUnwantedTvGenres()
    {
        return Cache::remember('unwanted_tv_genres', now()->addDay(), function () {
            $list = $this->request('/genre/tv/list');

            $excludeNames = [
                'Animation',
                'Kids',
                'News',
                'Reality',
                'Documentary',
                'Talk',
                'Soap',
            ];

            $genres = collect($list['genres'] ?? []);

            return $genres
                ->filter(fn ($genre) => in_array($genre['name'], $excludeNames))
                ->pluck('id')
                ->implode(',');
        });
    }

    protected function getUnwantedMovieGenres()
    {
        return Cache::remember('unwanted_movie_genres', now()->addDay(), function () {
            $list = $this->request('/genre/movie/list');

            $excludeNames = [
                'Animation',
                'Documentary',
                'Music',
            ];

            $genres = collect($list['genres'] ?? []);

            return $genres
                ->filter(fn ($genre) => in_array($genre['name'], $excludeNames))
                ->pluck('id')
                ->implode(',');
        });
    }

}
