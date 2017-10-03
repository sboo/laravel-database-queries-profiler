# Laravel 5 Database queries profiler

[![styleci](https://styleci.io/repos/105237482/shield)](https://styleci.io/repos/105237482)
[![Build Status](https://scrutinizer-ci.com/g/tarampampam/laravel-database-queries-profiler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tarampampam/laravel-database-queries-profiler/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/tarampampam/laravel-database-queries-profiler/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tarampampam/laravel-database-queries-profiler/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tarampampam/laravel-database-queries-profiler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tarampampam/laravel-database-queries-profiler/?branch=master)
[![GitHub issues](https://img.shields.io/github/issues/tarampampam/laravel-database-queries-profiler.svg?style=flat-square)](https://github.com/tarampampam/laravel-database-queries-profiler/issues)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/tarampampam/laravel-database-queries-profiler/master/license)

Lightweight database queries profiler. Use Laravel cache as data storage.

`->top()` data collector collects most "expensive" database queries (top `N` queries; count of "top" queries can be configured).

`->count()` data collector collects all queries and can calculate min/max/averaged queries duration time.
Also you can pass to the `->count(string $counter_name = null)` one of counters name:
 * `last_five_seconds`
 * `last_fifteen_seconds`
 * `last_minute`

### Install

Require this package with composer using the following command:

```bash
$ composer require tarampampam/laravel-database-queries-profiler
```

> For **Laravel v5.4** and lower:
> 
> After updating composer, add the service provider to the `providers` array in `config/app.php`:
> 
> ```php
> 'providers' => [
>     // ...
>     Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerServiceProvider::class,
> ]
> ```

And publish configs (and etc.):

```php
$ ./artisan vendor:publish --provider="Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerServiceProvider"
```

After that you will able to use next **artisan** commands:

 * `./artisan profiler:counters` - Show database queries durations counters
 * `./artisan profiler:top` - Show top (most "expensive") database queries
 * `./artisan profiler:settings` - Show database queries profiler settings
 * `./artisan profiler:clear` - Clear all aggregated profiler data

Also you can use next "helpers":
 * `dbProfilerRegisterQuery(DatabaseQuery|array $query)` - Handy register database query
 * `dbProfilerCounters(string $counter_name = null)` - Get the 'counters' aggregator
 * `dbProfilerTop()` - Get the 'top' aggregator
 
### Facade

Easy access to the profiler container:
```php
use Tarampampam\LaravelDatabaseQueriesProfiler\DatabaseQueriesProfilerFacade;

dump(DatabaseQueriesProfilerFacade::top()->toArray());
dump(DatabaseQueriesProfilerFacade::counters('last_fifteen_seconds')->getAveragedDuration());
```


### License

Laravel 5 Database queries profiler is open-sourced software licensed under the [MIT license](./LICENSE).