## Code structure
The code is structured as such:
```
api
-- commands
-- common
---- controllers
---- models
-- config
-- modules
---- v1
------ controllers
------ models
-- runtime
-- tests
-- web
vendor
```

- The `api` directory is basically the root of your application
- The `commands` folder contains all the console scripts
- The `common` directory contains all models and controllers that could be shared between different versions of your api
- The `modules` directory will contain the different versions of your api
- The `runtime` directory will stores cache and log files generated during runtime
- The `tests` directory will contain all your test files (Yii2 runs with [Codeception](http://codeception.com/))
- The `web` directory only contains the entry script of your api

## Simple Hello World
Once your webserver up and ready, you should be able to access the following urls:
- http://127.0.0.1/v1/hello
- http://127.0.0.1/v1/hello/you


