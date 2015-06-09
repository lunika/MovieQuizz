MovieQuizz
==========


## Configuration

you have to specify your **The Movie Database** API key as an [external parameter](http://symfony.com/doc/current/cookbook/configuration/external_parameters.html)

in your Virutal Host : 

```
SetEnv SYMFONY__TMDB__API__KEY your_api_key
```

and in your shell configuration : 

```
export SYMFONY__TMDB__API__KEY=your_api_key
```