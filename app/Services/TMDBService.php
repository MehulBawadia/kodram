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
        return $this->request('/discover/tv', [
            'air_date.lte' => today()->toDateString(),
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => 'popularity.desc',
            'with_runtime.gte' => 30,
            'vote_count.gte' => 50,
            'without_genres' => $this->getUnwantedTvGenres(),
        ]);
    }

    public function getPopularMovies()
    {
        $movies = $this->request('/discover/movie', [
            'air_date.lte' => today()->toDateString(),
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => 'popularity.desc',
            'vote_average.lte' => 10,
            'without_genres' => $this->getUnwantedMovieGenres(),
        ]);

        return collect($movies['results'] ?? [])
            ->filter(fn ($item) => ! empty($item['poster_path']))
            ->take(12)
            ->values()
            ->toArray();
    }

    protected function getUnwantedTvGenres()
    {
        return Cache::remember('unwanted_tv_genres', now()->addMinutes(1), function () {
            $list = $this->request('/genre/tv/list');

            $excludeNames = [
                'Animation',
                'Kids',
                'News',
                'Reality',
                'Documentary',
                'Talk',
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
        return Cache::remember('unwanted_movie_genres', now()->addMinutes(1), function () {
            $list = $this->request('/genre/tv/list');

            $excludeNames = [
                'Animation',
                'Documentary',
            ];

            $genres = collect($list['genres'] ?? []);

            return $genres
                ->filter(fn ($genre) => in_array($genre['name'], $excludeNames))
                ->pluck('id')
                ->implode(',');
        });
    }

}
