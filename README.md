MovieQuizz
==========

Small game testing cinematographic culture

## installation

```
$ git clone https://github.com/lunika/moviequizz
$ cd moviequizz
$ composer install
$ php app/console doctrine:database:create
$ php app/console doctrine:schema:update --force
```

## Configuration

you have to specify your [**The Movie Database**](https://www.themoviedb.org/) API key as an [external parameter](http://symfony.com/doc/current/cookbook/configuration/external_parameters.html)

in your Virutal Host : 

```
SetEnv SYMFONY__TMDB__API__KEY your_api_key
```

and in your shell configuration : 

```
export SYMFONY__TMDB__API__KEY=your_api_key
```

## Save movies and actors in database

If you want to start a party, you need first to retrieve some data from [**The Movie Database**](https://www.themoviedb.org/) API. 
You can use a command for this job : 

```
$ php app/console app:fetch-all --page=3
```

the ```page``` option is not mandatory and the default value is 1. Each page contains more or less 10 movies. This task can be long due to the API [request rate limiting](http://docs.themoviedb.apiary.io/#introduction/request-rate-limiting).

You can use this command in a cron if you want fresh data every day.

## Reset high score

If you want to reset highest scores saved in database, you can this command : 

```
$ php app/console app:reset-highscore
```

