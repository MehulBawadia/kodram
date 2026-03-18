<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\ConnectionException;

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
        $params = array_merge([
            'api_key' => $this->apiKey,
        ], $params);

        $cacheKey = 'tmdb_'.md5($endpoint.serialize($params));

        // Return cached if exists
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Small delay to prevent burst requests
        usleep(100000); // 100ms

        try {
            $response = Http::retry(
                3,
                fn ($attempt) => ($attempt * 300) + rand(50, 150),
                fn ($exception) => $exception instanceof ConnectionException
            )
                ->timeout(15)
                ->connectTimeout(5)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => config('app.name').'/1.0',
                    'Connection' => 'close',
                ])
                ->get($this->baseUrl.$endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();

                // Cache only successful responses
                Cache::put($cacheKey, $data, now()->addHour());

                return $data;
            }

            // Fallback to stale cache if exists
            $stale = Cache::get($cacheKey);
            if ($stale) {
                return $stale;
            }

            logger()->warning('TMDB API failed', [
                'endpoint' => $endpoint,
                'params' => $params,
                'status' => $response->status(),
            ]);

        } catch (\Throwable $e) {

            // Fallback to stale cache
            $stale = Cache::get($cacheKey);
            if ($stale) {
                return $stale;
            }

            logger()->error('TMDB connection error', [
                'endpoint' => $endpoint,
                'params' => $params,
                'message' => $e->getMessage(),
            ]);
        }

        return [];
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
                    'type' => 'movies',
                ];
            }));

            return $combined
                ->filter(fn ($item) =>
                    ! blank($item['backdrop_path']) || ! blank($item['poster_path'])
                )
                ->shuffle()
                ->take(4)
                ->values()
                ->toArray();
        });
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

    public function getLatestTvSeries()
    {
        return $this->request('/discover/tv', [
            'air_date.lte' => today()->toDateString(),
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => 'first_air_date.desc',
            'with_runtime.gte' => 30,
            'vote_count.gte' => 1,
            'without_genres' => $this->getUnwantedTvGenres(),
            'with_type' => '2|4', // 2 = miniseries, 4 = Scripted
            'with_status' => '3', // 0 = OnGoing, 1 = Upcoming, 2 = In Production, 3 = Ended
        ]);
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

        return $this->request('/discover/tv', $query);
    }

    public function getDramaDetails($id)
    {
        return $this->request("/tv/{$id}", [
            'append_to_response' => 'credits,images,similar',
        ]);
    }

    public function getDramaSeasonDetails($id, $seasonNumber)
    {
        return Cache::remember(
            "drama_season_details_{$id}_{$seasonNumber}",
            now()->addHours(8),
            fn () => $this->request("/tv/{$id}/season/{$seasonNumber}")
        );
    }

    public function getPopularMovies()
    {
        return $this->request('/discover/movie', [
            'primary_release_date.lte' => today()->toDateString(),
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => 'popularity.desc',
            'vote_average.lte' => 10,
            'without_genres' => $this->getUnwantedMovieGenres(),
            'without_keywords' => '155477',
        ]);
    }

    public function getLatestMovies()
    {
        return $this->request('/discover/movie', [
            'primary_release_date.lte' => today()->toDateString(),
            'with_original_language' => 'ko',
            'watch_region' => 'KR',
            'sort_by' => 'primary_release_date.desc',
            'vote_count.gte' => 1,
            'without_genres' => $this->getUnwantedMovieGenres(),
            'without_keywords' => '155477',
        ]);
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

        return $this->request('/discover/movie', $query);
    }

    public function getMovieDetails($id)
    {
        return $this->request("/movie/{$id}", [
            'append_to_response' => 'credits',
        ]);
    }

    public function search($query)
    {
        return $this->request('/search/multi', [
            'query' => $query,
        ])['results'] ?? [];
    }

    public function getPersonDetails($id)
    {
        return $this->request("/person/{$id}", [
            'append_to_response' => 'tv_credits,movie_credits,external_ids',
        ]);
    }

    public function getTvGenres()
    {
        $list = $this->request('/genre/tv/list');
        $genres = collect($list['genres'] ?? []);

        $unwanted = explode(',', $this->getUnwantedTvGenres());

        return $genres->filter(fn ($g) => ! in_array($g['id'], $unwanted));
    }

    public function getMoviesGenres()
    {
        $list = $this->request('/genre/movie/list');
        $genres = collect($list['genres'] ?? []);

        $unwanted = explode(',', $this->getUnwantedMovieGenres());

        return $genres->filter(fn ($g) => ! in_array($g['id'], $unwanted));
    }

    protected function getUnwantedTvGenres()
    {
        return Cache::remember('unwanted_tv_genres', now()->addDay(), function () {
            $list = $this->request('/genre/tv/list');

            return collect($list['genres'] ?? [])
                ->filter(fn ($g) => in_array($g['name'], [
                    'Animation', 'Kids', 'News', 'Reality', 'Documentary', 'Talk', 'Soap',
                ]))
                ->pluck('id')
                ->implode(',');
        });
    }

    protected function getUnwantedMovieGenres()
    {
        return Cache::remember('unwanted_movie_genres', now()->addDay(), function () {
            $list = $this->request('/genre/movie/list');

            return collect($list['genres'] ?? [])
                ->filter(fn ($g) => in_array($g['name'], [
                    'Animation', 'Documentary', 'Music',
                ]))
                ->pluck('id')
                ->implode(',');
        });
    }
}
